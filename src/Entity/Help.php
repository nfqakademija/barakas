<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelpRepository")
 */
class Help
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="helps")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $requester_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="helps")
     */
    private $user;

    public function __construct()
    {
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

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

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

    public function getRequesterId(): ?int
    {
        return $this->requester_id;
    }

    public function setRequesterId(int $requester_id): self
    {
        $this->requester_id = $requester_id;

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
