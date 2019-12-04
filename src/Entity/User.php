<?php

namespace App\Entity;

use Datetime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields = "owner", message="Šis vadovas jau užregistruotas!")
 * @UniqueEntity(fields = "email", message="Šis el. pašto adresas jau užregistruotas!")
 * @UniqueEntity(fields = "academy", message="Ši aukštoji mokykla jau užregistruota!
  Jei norite išsamesnės informacijos - susisiekite su administracija.")
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
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @Assert\Length(
     *     min = 7,
     *     max = 25,
     *     minMessage = "Vadovo vardas negali būti trumpesnis nei {{ limit }} simbolių.",
     *     maxMessage = "Vadovo vardas negali būti ilgesnis nei {{ limit }} simboliai."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $owner;

    /**
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Academy")
     * @ORM\JoinColumn(nullable=true)
     */
    private $academy;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dorm_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $room_nr;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $isDisabled;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user", cascade={"remove"})
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Help", mappedBy="user", cascade={"remove"})
     */
    private $helps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", cascade={"remove"})
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RoomChange", mappedBy="user", cascade={"remove"})
     */
    private $roomChanges;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * Date/Time of the last activity
     *
     * @var Datetime
     * @ORM\Column(name="last_activity_at", nullable=true, type="datetime")
     */
    protected $lastActivityAt;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->messages = new ArrayCollection();
        $this->helps = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->roomChanges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function generateRandomPassword()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function create($owner, $email, $academy, $password)
    {
        $self = new self();
        $self->owner = $owner;
        $self->email = $email;
        $self->academy = $academy;
        $self->password = $password;

        return $self;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->owner;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    public function getAcademy(): ?Academy
    {
        return $this->academy;
    }

    public function setAcademy(?Academy $academy): self
    {
        $this->academy = $academy;

        return $this;
    }

    public function getDormId(): ?int
    {
        return $this->dorm_id;
    }

    public function setDormId(?int $dorm_id): self
    {
        $this->dorm_id = $dorm_id;

        return $this;
    }

    public function getRoomNr(): ?string
    {
        return $this->room_nr;
    }

    public function setRoomNr(?string $room_nr): self
    {
        $this->room_nr = $room_nr;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

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
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Help[]
     */
    public function getHelps(): Collection
    {
        return $this->helps;
    }

    public function addHelp(Help $help): self
    {
        if (!$this->helps->contains($help)) {
            $this->helps[] = $help;
            $help->setUser($this);
        }

        return $this;
    }

    public function removeHelp(Help $help): self
    {
        if ($this->helps->contains($help)) {
            $this->helps->removeElement($help);
            // set the owning side to null (unless already changed)
            if ($help->getUser() === $this) {
                $help->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RoomChange[]
     */
    public function getRoomChanges(): Collection
    {
        return $this->roomChanges;
    }

    public function addRoomChange(RoomChange $roomChange): self
    {
        if (!$this->roomChanges->contains($roomChange)) {
            $this->roomChanges[] = $roomChange;
            $roomChange->setUser($this);
        }

        return $this;
    }

    public function removeRoomChange(RoomChange $roomChange): self
    {
        if ($this->roomChanges->contains($roomChange)) {
            $this->roomChanges->removeElement($roomChange);
            // set the owning side to null (unless already changed)
            if ($roomChange->getUser() === $this) {
                $roomChange->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    /**
     * @param mixed $isDisabled
     */
    public function setIsDisabled($isDisabled): void
    {
        $this->isDisabled = $isDisabled;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return Datetime
     */
    public function getLastActivityAt(): ?Datetime
    {
        return $this->lastActivityAt;
    }

    /**
     * @param Datetime $lastActivityAt
     */
    public function setLastActivityAt(Datetime $lastActivityAt): void
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    public function isActiveNow()
    {
        $delay = new \DateTime('2 minutes ago');

        return ( $this->getLastActivityAt() > $delay );
    }
}
