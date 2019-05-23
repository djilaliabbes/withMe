<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\ImageValidator;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * message="l'email que vous avez indiquer est déjà utilisé"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="Votre Mot de passe doit faire au minimum 8 caractères ")
     */
    private $password;
    /**
     * @Assert\EqualTo(propertyPath="password",message="Vous n'avez pas taper le même mot de passe")
     */
    public $confirm_password;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleLike", mappedBy="user")
     */
    private $articleLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $sexe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activite", inversedBy="users")
     */
    private $activite;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $tel;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invitation", mappedBy="userQuiInvite")
     */
    private $invitationsEnvoyer;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invitation", mappedBy="userInvite")
     */
    private $invitationsRecu;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ami", mappedBy="user")
     * @ORM\OneToMany(targetEntity="App\Entity\Ami", mappedBy="ami")
     */
    private $amis;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="userFrom")
     */
    private $messages;

    public function __construct()
    {
        $this->articleLikes = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->activite = new ArrayCollection();
        $this->invitationsEnvoyer = new ArrayCollection();
        $this->invitationsRecu = new ArrayCollection();
        $this->amis = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function eraseCredentials()
    { }
    public function getSalt()
    { }
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|ArticleLike[]
     */
    public function getArticleLikes(): Collection
    {
        return $this->articleLikes;
    }

    public function addArticleLike(ArticleLike $articleLike): self
    {
        if (!$this->articleLikes->contains($articleLike)) {
            $this->articleLikes[] = $articleLike;
            $articleLike->setUser($this);
        }

        return $this;
    }

    public function removeArticleLike(ArticleLike $articleLike): self
    {
        if ($this->articleLikes->contains($articleLike)) {
            $this->articleLikes->removeElement($articleLike);
            // set the owning side to null (unless already changed)
            if ($articleLike->getUser() === $this) {
                $articleLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection|Activite[]
     */
    public function getActivite(): Collection
    {
        return $this->activite;
    }

    public function addActivite(Activite $activite): self
    {
        if (!$this->activite->contains($activite)) {
            $this->activite[] = $activite;
        }

        return $this;
    }

    public function removeActivite(Activite $activite): self
    {
        if ($this->activite->contains($activite)) {
            $this->activite->removeElement($activite);
        }

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }


    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsEnvoyer(): Collection
    {
        return $this->invitationsEnvoyer;
    }

    public function addInvitationEnvoyer(Invitation $invitation): self
    {
        if (!$this->invitationsEnvoyer->contains($invitation)) {
            $this->invitationsEnvoyer[] = $invitation;
            $invitation->setUserQuiInvite($this);
        }

        return $this;
    }

    public function removeInvitationEnvoyer(Invitation $invitation): self
    {
        if ($this->invitationsEnvoyer->contains($invitation)) {
            $this->invitationsEnvoyer->removeElement($invitation);
        }
        return $this;
    }

    /**
     * @return Collection|Invitation[]
     */
    public function getInvitationsRecu(): Collection
    {
        return $this->invitationsRecu;
    }

    public function addInvitationRecu(Invitation $invitation): self
    {
        if (!$this->invitationsRecu->contains($invitation)) {
            $this->invitationsRecu[] = $invitation;
            $invitation->setUserInvite($this);
        }

        return $this;
    }

    public function removeInvitationsRecu(Invitation $invitation): self
    {
        if ($this->invitationsRecu->contains($invitation)) {
            $this->invitationsRecu->removeElement($invitation);
            // set the owning side to null (unless already changed)
            if ($invitation->getUserInvite() === $this) {
                $invitation->setUserInvite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ami[]
     */
    public function getAmis(): Collection
    {
        return $this->amis;
    }

    public function addAmi(Ami $ami): self
    {
        if (!$this->amis->contains($ami)) {
            $this->amis[] = $ami;
            $ami->setUser($this);
        }

        return $this;
    }

    public function removeAmi(Ami $ami): self
    {
        if ($this->amis->contains($ami)) {
            $this->amis->removeElement($ami);
            // set the owning side to null (unless already changed)
            if ($ami->getUser() === $this) {
                $ami->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUserFrom($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUserFrom() === $this) {
                $message->setUserFrom(null);
            }
        }

        return $this;
    }
}
