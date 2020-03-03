<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(name="email", type="string", unique=true)
     * @Assert\Email
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom de famille ne peut pas être vide")
     * @Assert\Length(min="2", minMessage="Le nom est trop petit",
     *      max="255", maxMessage="Le nom est trop long")
     */
    private $lastName;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le prénom ne peut pas être vide")
     * @Assert\Length(min="2", minMessage="Le prénom est trop petit",
     *      max="255", maxMessage="Le prénom est trop long")
     */
    private $firstName;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthDate;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creationDate;
    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SocialMedia", mappedBy="user")
     */
    private $socialMedia;
    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->comment = new ArrayCollection();
        $this->post = new ArrayCollection();
        $this->socialMedia = new ArrayCollection();
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
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }
    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }
    public function setBirthDate(?\DateTimeInterface $BirthDate): self
    {
        $this->birthDate = $BirthDate;
        return $this;
    }
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }
    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
        return $this;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }
    public function getSalt()
    {
        return null;
    }
    public function getUsername()
    {
        return $this->email;
    }
    public function eraseCredentials()
    {
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->contains($comment)) {
            $this->comment->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->post->contains($post)) {
            $this->post->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SocialMedia[]
     */
    public function getSocialMedia(): Collection
    {
        return $this->socialMedia;
    }

    public function addSocialMedium(SocialMedia $socialMedium): self
    {
        if (!$this->socialMedia->contains($socialMedium)) {
            $this->socialMedia[] = $socialMedium;
            $socialMedium->setUser($this);
        }

        return $this;
    }

    public function removeSocialMedium(SocialMedia $socialMedium): self
    {
        if ($this->socialMedia->contains($socialMedium)) {
            $this->socialMedia->removeElement($socialMedium);
            // set the owning side to null (unless already changed)
            if ($socialMedium->getUser() === $this) {
                $socialMedium->setUser(null);
            }
        }

        return $this;
    }
}