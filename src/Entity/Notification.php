<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
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
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $room_nr;

    /**
     * @ORM\Column(type="integer")
     */
    private $dorm_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $recipient_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

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

    public function getDormId(): ?int
    {
        return $this->dorm_id;
    }

    public function setDormId(int $dorm_id): self
    {
        $this->dorm_id = $dorm_id;

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

    public function getRecipientId(): ?int
    {
        return $this->recipient_id;
    }

    public function setRecipientId(int $recipient_id): self
    {
        $this->recipient_id = $recipient_id;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }
}
