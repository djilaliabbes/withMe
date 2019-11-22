<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 */
class Invitation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invitations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userQuiInvite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invitations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userInvite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserQuiInvite(): ?User
    {
        return $this->userQuiInvite;
    }

    public function setUserQuiInvite(?User $userQuiInvite): self
    {
        $this->userQuiInvite = $userQuiInvite;

        return $this;
    }

    public function getUserInvite(): ?User
    {
        return $this->userInvite;
    }

    public function setUserInvite(?User $userInvite): self
    {
        $this->userInvite = $userInvite;

        return $this;
    }
}
