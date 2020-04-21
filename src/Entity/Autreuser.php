<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AutreuserRepository")
 *  @UniqueEntity(
 *     fields={"email"},
 *     message="Je pense que vous êtes déjà enregistré!"
 * )
 */
class Autreuser implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("main")
     * @Assert\NotBlank(message="Entrez un email, s'il vous plait")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $agreedTermsAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rne;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;
    
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // pas nécessaire en utilisant argon
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAgreedTermsAt(): ?\DateTimeInterface
    {
        return $this->agreedTermsAt;
    }

    public function setAgreedTermsAt(\DateTimeInterface $agreedTermsAt): self
    {
        $this->agreedTermsAt = $agreedTermsAt;

        return $this;
    }
    
        public function agreeTerms()
    {
        $this->agreedTermsAt = new \DateTime();
    }

        public function getRne(): ?string
        {
            return $this->rne;
        }

        public function setRne(?string $rne): self
        {
            $this->rne = $rne;

            return $this;
        }

        public function getIsActive(): ?bool
        {
            return $this->isActive;
        }

        public function setIsActive(bool $isActive): self
        {
            $this->isActive = $isActive;

            return $this;
        }

        public function getToken(): ?string
        {
            return $this->token;
        }

        public function setToken(?string $token): self
        {
            $this->token = $token;

            return $this;
        }

        public function getPasswordRequestedAt(): ?\DateTimeInterface
        {
            return $this->passwordRequestedAt;
        }

        public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
        {
            $this->passwordRequestedAt = $passwordRequestedAt;

            return $this;
        }

        public function getPrenom(): ?string
        {
            return $this->prenom;
        }

        public function setPrenom(?string $prenom): self
        {
            $this->prenom = $prenom;

            return $this;
        }

        public function getAdresse(): ?string
        {
            return $this->adresse;
        }

        public function setAdresse(?string $adresse): self
        {
            $this->adresse = $adresse;

            return $this;
        }

        public function getVille(): ?string
        {
            return $this->ville;
        }

        public function setVille(?string $ville): self
        {
            $this->ville = $ville;

            return $this;
        }

        public function getCode(): ?string
        {
            return $this->code;
        }

        public function setCode(?string $code): self
        {
            $this->code = $code;

            return $this;
        }

        public function getPhone(): ?string
        {
            return $this->phone;
        }

        public function setPhone(?string $phone): self
        {
            $this->phone = $phone;

            return $this;
        }
}
