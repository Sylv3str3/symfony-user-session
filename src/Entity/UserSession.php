<?php

namespace UserSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserSessionBundle\Model\AbstractUserSession;

#[ORM\Entity]
#[ORM\Table(name: 'user_sessions')]
class UserSession extends AbstractUserSession
{
    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(nullable: false)]
    private object $user;

    public function getUser(): object
    {
        return $this->user;
    }

    public function setUser(object $user): static
    {
        $this->user = $user;
        return $this;
    }
}
