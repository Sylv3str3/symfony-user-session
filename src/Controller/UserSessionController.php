<?php

namespace UserSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use UserSessionBundle\Service\UserSessionManager;

class UserSessionController extends AbstractController
{
    public function __construct(
        private readonly UserSessionManager $sessionManager,
    ) {}



    public function deleteSession(string $sessionId): JsonResponse
    {
        $session = $this->sessionManager->getSessionById($sessionId);

        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }
        $deleted = $this->sessionManager->deleteSession($sessionId);
        return $this->json(['success' => $deleted]);
    }

    public function revokeAllSessions(): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        $this->sessionManager->deleteAllUserSessions($user);

        return $this->json(['success' => true]);
    }

    public function listAllSessions(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, min(50, $request->query->getInt('limit', 10)));

        $result = $this->sessionManager->listAllSessionsPaginated($page, $limit);
        
        return $this->json([
            'sessions' => array_map([$this, 'formatSession'], $result['items']),
            'pagination' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'pages' => $result['pages']
            ]
        ]);
    }

    public function listUserSessions(Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, min(50, $request->query->getInt('limit', 10)));
        
        /** @var UserInterface $user */
        $user = $this->getUser();
        $result = $this->sessionManager->listUserSessionsPaginated($user, $page, $limit);
        
        return $this->json([
            'sessions' => array_map([$this, 'formatSession'], $result['items']),
            'pagination' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'pages' => $result['pages']
            ]
        ]);
    }

    public function getSession(string $sessionId): JsonResponse
    {
        $session = $this->sessionManager->getSessionById($sessionId);

        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        return $this->json(['session' => $this->formatSession($session)]);
    }

    public function deleteAllUserSessions(): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        $this->sessionManager->deleteAllUserSessions($user);

        return $this->json(['success' => true]);
    }

    private function formatSessions(array $sessions): array
    {
        return array_map(fn($session) => $this->formatSession($session), $sessions);
    }

    private function formatSession($session): array
    {
        return [
            'id' => $session->getSessionId(),
            'userId' => $session->getUser()->getId(),
            'provider' => $session->getProvider(),
            'deviceInfo' => [
                'userAgent' => $session->getUserAgent(),
                'deviceId' => $session->getDeviceId()
            ],
            'createdAt' => $session->getCreatedAt()->format('c'),
            'lastActiveAt' => $session->getLastActiveAt()->format('c')
        ];
    }
}
