<?php

namespace UserSessionBundle\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\MappedSuperclass]
abstract class AbstractUserSession
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected Uuid $sessionId;

    #[ORM\Column(length: 255)]
    protected string $provider;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $userAgent = null;

    #[ORM\Column(length: 255)]
    protected string $deviceId;

    #[ORM\Column]
    protected DateTimeImmutable $createdAt;

    #[ORM\Column]
    protected DateTimeImmutable $lastActiveAt;

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

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function setDeviceId(string $deviceId): static
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

    public function updateLastActiveAt(): static
    {
        $this->lastActiveAt = new DateTimeImmutable();
        return $this;
    }

    abstract public function getUser(): object;
    abstract public function setUser(object $user): static;
}
