<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AcademyRepository")
 */
class Academy
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
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $academyType;

    /**
     * @return int|null
     * @var int|string
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAcademyType()
    {
        return AcademyType::fromId($this->academyType);
    }

    public function setAcademyType(AcademyType $academyType)
    {
        $this->academyType = $academyType->id();

        return $this;
    }
}
