<?php

namespace UserSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'user_sessions')]
class UserSession
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $sessionId;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 20)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userAgent;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $deviceId;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $lastActiveAt;

    public function __construct()
    {
        $this->sessionId = Uuid::v4();
        $this->createdAt = new DateTimeImmutable();
        $this->lastActiveAt = new DateTimeImmutable();
    }

    public function getSessionId(): Uuid
    {
        return $this->sessionId;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function setDeviceId(?string $deviceId): self
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastActiveAt(): DateTimeImmutable
    {
        return $this->lastActiveAt;
    }

    public function updateLastActiveAt(): self
    {
        $this->lastActiveAt = new DateTimeImmutable();
        return $this;
    }
}
