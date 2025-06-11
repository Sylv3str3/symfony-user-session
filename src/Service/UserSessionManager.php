<?php

namespace UserSessionBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UserSessionBundle\Entity\UserSession;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UserSessionBundle\Event\UserSessionCreatedEvent;
use UserSessionBundle\Event\UserSessionDeletedEvent;
use UserSessionBundle\Event\UserSessionInvalidatedEvent;
use UserSessionBundle\Events;

class UserSessionManager
{
    private int $maxSessionsPerUser;
    private int $updateThresholdSeconds;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly EntityManagerInterface $entityManager,
        int $maxSessionsPerUser = 5,
        int $updateThresholdSeconds = 300
    ) {
        $this->maxSessionsPerUser = $maxSessionsPerUser;
        $this->updateThresholdSeconds = $updateThresholdSeconds;
    }

    public function createSession(object $user, string $provider, Request $request): UserSession
    {
        $this->cleanOldSessions($user);

        $session = new UserSession();
        $session->setUser($user)
            ->setProvider($provider)
            ->setUserAgent($request->headers->get('User-Agent'))
            ->setDeviceId($this->generateDeviceFingerprint($request));

        $this->entityManager->persist($session);
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new UserSessionCreatedEvent($session), Events::USER_SESSION_CREATED);

        return $session;
    }

    public function validateSession(string $sessionId): ?UserSession
    {
        $session = $this->entityManager->getRepository(UserSession::class)
            ->find($sessionId);

        if (!$session) {
            $this->dispatcher->dispatch(new UserSessionInvalidatedEvent($sessionId), Events::USER_SESSION_INVALIDATED);

            return null;
        }

        $now = new \DateTimeImmutable();
        $lastActiveAt = $session->getLastActiveAt();

        // Évite de flusher la BDD à chaque requête
        if ($lastActiveAt->getTimestamp() < $now->getTimestamp() - $this->updateThresholdSeconds) {
            $session->updateLastActiveAt();
            $this->entityManager->flush();
        }

        return $session;
    }

    public function deleteSession(string $sessionId): bool
    {
        $session = $this->entityManager->getRepository(UserSession::class)
            ->find($sessionId);

        if (!$session) {
            return false;
        }

        $this->entityManager->remove($session);
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new UserSessionDeletedEvent($sessionId), Events::USER_SESSION_DELETED);

        return true;
    }

    public function deleteAllUserSessions(object $user): void
    {
        $sessions = $this->entityManager->getRepository(UserSession::class)
            ->findBy(['user' => $user]);

        foreach ($sessions as $session) {
            $this->entityManager->remove($session);
        }

        $this->entityManager->flush();
    }

    /**
     * Liste toutes les sessions de tous les utilisateurs
     */
    public function listAllSessions(): array
    {
        return $this->entityManager->getRepository(UserSession::class)
            ->findBy([], ['lastActiveAt' => 'DESC']);
    }

    /**
     * Liste toutes les sessions d'un utilisateur spécifique
     */
    public function listUserSessions(object $user): array
    {
        return $this->entityManager->getRepository(UserSession::class)
            ->findBy(
                ['user' => $user],
                ['lastActiveAt' => 'DESC']
            );
    }

    /**
     * Récupère une session par son ID
     */
    public function getSessionById(string $sessionId): ?UserSession
    {
        return $this->entityManager->getRepository(UserSession::class)
            ->findOneBy(['id'=> $sessionId]);
    }

    /**
     * Compte le nombre de sessions actives pour un utilisateur
     */
    public function countActiveSessions(object $user): int
    {
        return $this->entityManager->getRepository(UserSession::class)
            ->count(['user' => $user]);
    }

    /**
     * Vérifie si un utilisateur peut créer une nouvelle session
     */
    public function canCreateNewSession(object $user): bool
    {
        return $this->countActiveSessions($user) < $this->maxSessionsPerUser;
    }

    private function cleanOldSessions(object $user): void
    {
        $sessions = $this->entityManager->getRepository(UserSession::class)
            ->findBy(
                ['user' => $user],
                ['lastActiveAt' => 'DESC']
            );

        if (count($sessions) >= $this->maxSessionsPerUser) {
            for ($i = $this->maxSessionsPerUser - 1; $i < count($sessions); $i++) {
                $this->entityManager->remove($sessions[$i]);
            }
            $this->entityManager->flush();
        }
    }

    private function generateDeviceFingerprint(Request $request): string
    {
        $data = [
            $request->headers->get('User-Agent'),
            $request->getClientIp(),
            $request->headers->get('Accept-Language'),
        ];

        return hash('sha256', implode('|', $data));
    }

    /**
     * Liste toutes les sessions avec pagination
     */
    public function listAllSessionsPaginated(int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
           ->from(UserSession::class, 's')
           ->orderBy('s.lastActiveAt', 'DESC')
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery());

        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil(count($paginator) / $limit)
        ];
    }

    /**
     * Liste les sessions d'un utilisateur avec pagination
     */
    public function listUserSessionsPaginated(object $user, int $page = 1, int $limit = 10): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
           ->from(UserSession::class, 's')
           ->where('s.user = :user')
           ->setParameter('user', $user)
           ->orderBy('s.lastActiveAt', 'DESC')
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $paginator = new Paginator($qb->getQuery());

        return [
            'items' => iterator_to_array($paginator),
            'total' => count($paginator),
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil(count($paginator) / $limit)
        ];
    }
}
