<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganisationRepository")
 * @UniqueEntity(fields = "owner", message="Šis vadovas jau užregistruotas!")
 * @UniqueEntity(fields = "email", message="Šis el. pašto adresas jau užregistruotas!")
 * @UniqueEntity(fields = "academy", message="Ši aukštoji mokykla jau užregistruota!
  Jei norite išsamesnės informacijos - susisiekite su administracija.")
 */
class Organisation implements UserInterface
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
     * @Assert\NotNull(message="Šis laukelis yra privalomas.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Academy")
     * @ORM\JoinColumn(nullable=false)
     */
    private $academy;

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
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
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
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
}
