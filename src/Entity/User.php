<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Cet email est déjà enregistré en base.")
 * @UniqueEntity(fields="username", message="Cet identifiant est déjà enregistré en base")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $username;

     /**
     * * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    

    private $plainPassword;
    
     /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=60)
     * @Assert\Email()
     */
    private $email;
 
     /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $token;
    
     /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passwordRequestedAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rne", type="string", length=255, nullable=true)
     */
    protected $rne;
    
     /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    protected $nom;
    
     /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    protected $prenom;  
    
     /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    protected $adresse;
    
     /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    protected $ville;
    
     /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=11, nullable=true)
     */
    protected $code;
    
     /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    protected $phone;
   
     /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Centrescia")
       * @ORM\JoinColumn(name="centre_id",  referencedColumnName="id" )
       */       
    private $centrecia;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastVisit", type="datetime", nullable=true)
     */
    private $lastVisit;
    
     /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=15, nullable=true)
     */
    protected $civilite;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = ['ROLE_USER'];
       
        
    }
     

    public function getId(): ?int
    {
        return $this->id;
    }
   
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    
     /*
     * Get email
     */
    public function getCentrecia()
    {
        return $this->centrecia;
    }
 
    /*
     * Set email
     */
    public function setCentrecia($centrecia)
    {
        $this->centrecia= $centrecia;
        return $this;
    }
    

    /*
     * Get email
     */
    public function getEmail()
    {
        return $this->email;
    }
 
    /*
     * Set email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
 
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @param string $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }  
    
    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        return $this->roles; 
    }

    public function setRoles(array $roles)
    {
        if (!in_array('ROLE_USER', $roles))
        {
            $roles[] = 'ROLE_USER';
        }
        foreach ($roles as $role)
        {
            if(substr($role, 0, 5) !== 'ROLE_') {
                throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
            }
        }
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /*
     * Get isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
 
    /*
     * Set isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
    /*
     * Get passwordRequestedAt
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /*
     * Set passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt,
        ));
    }
 
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // voir remarques sur salt plus haut
            // $this->salt
        ) = unserialize($serialized);
    }
    
        /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(max=4096)
     */
 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
 
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    
     /**
     * Set rne
     *
     * @param string $rne
     *
     * @return User
     */
    public function setRne( $rne) {
        $this->rne= $rne;

        return $this;
    }

    /**
     * Get Adresse
     *
     * @return string
     */
    public function getAdresse() {
        return $this->adresse;
    }
    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return User
     */
    public function setAdresse( $adresse) {
        $this->adresse= $adresse;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille() {
        return $this->ville;
    }
    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return User
     */
    public function setVille( $ville) {
        $this->ville= $ville;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }
    /**
     * Set Code
     *
     * @param string $code
     *
     * @return User
     */
    public function setCode( $code) {
        $this->code= $code;

        return $this;
    }
    
    /**
     * Get 
     *
     * @return string
     */
    public function getCivilite() {
        return $this->civilite;
    }
    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return User
     */
    public function setCivilite( $civilite) {
        $this->civilite= $civilite;

        return $this;
    }
     /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    /**
     * Set phone
     *
     * @param string $code
     *
     * @return User
     */
    public function setPhone( $phone) {
        $this->phone= $phone;

        return $this;
    }

    /**
     * Get rne
     *
     * @return string
     */
    public function getRne() {
        return $this->rne;
    }
    
    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom( $nom) {
        $this->nom= $nom;

        return $this;
    }

    
    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom() {
        return $this->prenom;
    }
    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom( $prenom) {
        $this->prenom= $prenom;

        return $this;
    }

     /*
     * Get createdAt
     */
    public function getCreatedAt()
    {
        return $this->creaatedAt;
    }

    /*
     * Set updatedAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    /*
     * Get updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /*
     * Set updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $passwordRequestedAt;
        return $this;
    }
    
     /* Get lastVisit
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /*
     * Set lastVisit
     */
    public function setLastVisit()
    {
        $this->lastVisit = $lastVisit;
        return $this;
    }
    
    
}
