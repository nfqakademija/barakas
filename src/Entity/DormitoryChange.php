<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DormitoryChangeRepository")
 */
class DormitoryChange
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="dormitoryChange", cascade={"remove"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dormitory")
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     */
    private $dormitory;

    /**
     * @ORM\Column(type="integer")
     */
    private $approved;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Academy")
     */
    private $academy;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     */
    private $room_nr;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @Assert\Length(
     *     min = 7,
     *     minMessage = "Keitimo priežastis negali būti trumpesnė nei {{ limit }} simboliai.",
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getDormitory(): ?Dormitory
    {
        return $this->dormitory;
    }

    public function setDormitory(?Dormitory $dormitory): self
    {
        $this->dormitory = $dormitory;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
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

    public function getRoomNr(): ?string
    {
        return $this->room_nr;
    }

    public function setRoomNr(string $room_nr): self
    {
        $this->room_nr = $room_nr;

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
