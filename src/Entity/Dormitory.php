<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DormitoryRepository")
 */
class Dormitory
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
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $organisation_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId(): self
    {
        return $this->organisation_id;
    }

    /**
     * @param mixed $organisation_id
     * @return Dormitory
     */
    public function setOrganisationId($organisation_id): self
    {
        $this->organisation_id = $organisation_id;

        return $this;
    }

}
