<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InviteRepository")
 * @UniqueEntity(fields = "email", message="Šis studentas jau užregistruotas!")
 */
class Invite
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
     *     min = 10,
     *     max = 25,
     *     minMessage = "Studento vardas negali būti trumpesnis nei {{ limit }} simbolių.",
     *     maxMessage = "Studento vardas negali būti ilgesnis nei {{ limit }} simboliai."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;
    
    /**
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @ORM\Column(type="string", length=255)
     */
    private $email;
    
     /**
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @ORM\Column(type="string", length=255)
     */
    private $room;
    
     /**
     * @ORM\Column(type="integer", length=255)
     */
    private $dorm;
    
    public function generateUrl()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 15; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDorm(): ?int
    {
        return $this->dorm;
    }

    public function setDorm(int $dorm): self
    {
        $this->dorm = $dorm;

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
    
    public function getUrl(): ?string
    {
        return $this->url;
    }
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
    
    public function getRoom(): ?string
    {
        return $this->room;
    }
    public function setRoom(string $room): self
    {
        $this->room = $room;

        return $this;
    }
}
