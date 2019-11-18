<?php

namespace App\Entity;

use Cassandra\Date;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $dorm_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $room_nr;

    /**
     * @Assert\Length(
     *     min = 7,
     *     max = 300,
     *     minMessage = "Prašymą turi sudaryti mažiausiai {{ limit }} simboliai.",
     *     maxMessage = "Prašymas neturi būti ilgesnis nei {{ limit }} simbolių."
     * )
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $solved;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="message")
     */
    private $notifications;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
     */
    private $user;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDormId(): ?int
    {
        return $this->dorm_id;
    }

    public function setDormId(int $dorm_id): self
    {
        $this->dorm_id = $dorm_id;

        return $this;
    }

    public function getRoomNr(): ?string
    {
        return $this->room_nr;
    }

    public function setRoomNr(string $room_nr): self
    {
        $this->room_nr = $room_nr;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(StatusType $statusType)
    {
        $this->status = $statusType->id();

        return $this;
    }

    public function getSolved(): ?int
    {
        return $this->solved;
    }

    public function setSolved(SolvedType $solvedType)
    {
        $this->solved = $solvedType->id();

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
            $notification->setMessage($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getMessage() === $this) {
                $notification->setMessage(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
