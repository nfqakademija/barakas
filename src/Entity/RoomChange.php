<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomChangeRepository")
 */
class RoomChange
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
    private $current_room;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     */
    private $new_room_nr;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="roomChanges, cascade={"remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $approved;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Academy")
     */
    private $academy;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @Assert\Length(
     *     min = 7,
     *     minMessage = "Keitimo priežastis negali būti trumpesnė nei {{ limit }} simboliai.",
     * )
     */
    private $description;


    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentRoom(): ?string
    {
        return $this->current_room;
    }

    public function setCurrentRoom(string $current_room): self
    {
        $this->current_room = $current_room;

        return $this;
    }

    public function getNewRoomNr(): ?string
    {
        return $this->new_room_nr;
    }

    public function setNewRoomNr(string $new_room_nr): self
    {
        $this->new_room_nr = $new_room_nr;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApproved(): ?int
    {
        return $this->approved;
    }

    public function setApproved(ApprovedType $approvedType): void
    {
        $this->approved = $approvedType->id();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
