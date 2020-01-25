<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eleves
 *
 * @ORM\Table(name="elevesinter")
 * @ORM\Entity(repositoryClass="App\Repository\ElevesinterRepository")
 */
class Elevesinter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numsite", type="integer", nullable=true)
     * 
     */
    private $numsite;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private $prenom;
    /**
     * @var string
     * @ORM\Column(name="genre", type="string", length=1, nullable=true)
     */
    private $genre;
    

    /**
     * @var string
     *
     * @ORM\Column(name="classe", type="string", length=255, nullable=true)
     */
     private $classe;
    
     
       /**
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id" )
       */
     private $equipe;
    
     /**
     * @var string
     *
     * @ORM\Column(name="courriel", type="string",length=60, nullable=true)
     */
      private $courriel;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get numsite
     *
     * @return integer
     */
    public function getNumsite()
    {
        return $this->numsite;
    }
    
    
    /**
     * Set numsite
     *
     * @var integer
     */
    public function setNumsite($numsite)
    {
        $this->numsite=$numsite;
    }
    
    

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Eleves
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set classe
     *
     * @param string $classe
     *
     * @return Eleves
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    
    public function setEquipe($Equipe)
    {
        $this->equipe = $Equipe;

        return $this;
    }
   
    public function getEquipe()
    {
        return $this->equipe;
    }
    
   
    
     
    public function getCourriel()
    {
        return $this->courriel;
    }
    
    
    public function setCourriel($courriel)
    {
        $this->courriel = $courriel;

        return $this;
    }
    public function getGenre()
    {
        return $this->genre;
    }
    
    
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }
    
}
