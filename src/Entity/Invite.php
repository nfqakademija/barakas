<?php

namespace App\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InviteRepository")
 * @UniqueEntity(fields = "mail", message="Šis studentas jau užregistruotas!")
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
    private $email;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url; 
    
    public function generateUrl()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
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
}