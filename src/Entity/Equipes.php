<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Equipes
 * @Vich\Uploadable
 * @ORM\Table(name="equipes")
 * @ORM\Entity(repositoryClass="App\Repository\EquipesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Equipes
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
     * @var string
     *
     * @ORM\Column(name="lettre", type="string", length=255, unique=true)
     */
    private $lettre;

    /**
     * @var string
     *
     * @ORM\Column(name="titreProjet", type="string", length=255, unique=true)
     */
    private $titreProjet; 
    
     /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="smallint",nullable=true)
     */
    private $ordre; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="heure", type="string", length=255, nullable=true)
     */
    private $heure; 
    
     /**
     * @var string
     *
     * @ORM\Column(name="salle", type="string", length=255, nullable=true)
     */
    private $salle; 
    
     /**
     * @var int
     *
     * @ORM\Column(name="total", type="smallint", nullable=true)
     */
    private $total;

    /**
     * @var string
     * @ORM\Column(name="classement", type="string", length=255, nullable=true)
     */
    private $classement;

    /**
     * @var int
     * @ORM\Column(name="rang", type="smallint", nullable=true)
     */
    private $rang;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Visites", cascade={"persist"})
     */
    private $visite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cadeaux", cascade={"persist"})
     */
    private $cadeau;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Phrases", cascade={"persist"})
     */
    private $phrases;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Liaison", cascade={"persist"})
     */
    private $liaison;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Prix", cascade={"persist"})
     */
    private $prix;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Equipesadmin", cascade={"persist"})
     */
    private $infoequipe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Eleves", mappedBy="lettreEquipe")
     */
    private $eleves;  // notez le "s" : une equipe est liée à plusieurs eleves. 

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="equipe")
     */
    private $notess;  // notez le "s" : une equipe est liée à plusieurs lignes de "notes". 

    /**
     * @ORM\Column(name="nb_notes", type="integer")
     */
    private $nbNotes=0;  
  
    
     
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notess = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        
    }
    

   public function increaseNbNotes()
   {
       $this->nbNotes++;
   }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lettre
     *
     * @param string $lettre
     *
     * @return Equipes
     */
    public function setLettre($lettre)
    {
        $this->lettre = $lettre;

        return $this;
    }

    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre()
    {
        return $this->lettre;
    }

    /**
     * Set titreProjet
     *
     * @param string $titreProjet
     *
     * @return Equipes
     */
    public function setTitreProjet($titreProjet)
    {
        $this->titreProjet = $titreProjet;

        return $this;
    }

    /**
     * Get titreProjet
     *
     * @return string
     */
    public function getTitreProjet()
    {
        return $this->titreProjet;
    }

        /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return Equipes
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }
    
        /**
     * Set heure
     *
     * @param string $heure
     *
     * @return Equipes
     */
    public function setHeure($heure)
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return string
     */
    public function getHeure()
    {
        return $this->heure;
    }
    
        /**
     * Set salle
     *
     * @param string $salle
     *
     * @return Equipes
     */
    public function setSalle($salle)
    {
        $this->salle = $salle;

        return $this;
    }

    /**
     * Get salle
     *
     * @return string
     */
    public function getSalle()
    {
        return $this->salle;
    }
    

    /**
     * Set visite
     *
     * @param \App\Entity\Visites $visite
     *
     * @return Equipes
     */
    public function setVisite(\App\Entity\Visites $visite = null)
    {
        $this->visite = $visite;

        return $this;
    }

    /**
     * Get visite
     *
     * @return \App\Entity\Visites
     */
    public function getVisite()
    {
        return $this->visite;
    }

    /**
     * Add notess
     *
     * @param \App\Entity\Notes $notess
     *
     * @return Equipes
     */
    public function addNotess(\App\Entity\Notes $notess)
    {
        $this->notess[] = $notess;

        //On relie l'équipe à "une ligne note"
        $notess->setEquipe($this);

        return $this;
    }

    /**
     * Remove notess
     *
     * @param \App\Entity\Notes $notess
     */
    public function removeNotess(\App\Entity\Notes $notess)
    {
        $this->notess->removeElement($notess);
    }

    /**
     * Get notess
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotess()
    {
        return $this->notess;
    }

    /**
     * Set nbNotes
     *
     * @param integer $nbNotes
     *
     * @return Equipes
     */
    public function setNbNotes($nbNotes)
    {
        $this->nbNotes = $nbNotes;

        return $this;
    }

    /**
     * Get nbNotes
     *
     * @return integer
     */
    public function getNbNotes()
    {
        return $this->nbNotes;
    }

    /**
     * Set cadeau
     *
     * @param \App\Entity\Cadeaux $cadeau
     *
     * @return Equipes
     */
    public function setCadeau(\App\Entity\Cadeaux $cadeau = null)
    {
        $this->cadeau = $cadeau;

        return $this;
    }

    /**
     * Get cadeau
     *
     * @return \App\Entity\Cadeaux
     */
    public function getCadeau()
    {
        return $this->cadeau;
    }

    /**
     * Set phrases
     *
     * @param \App\Entity\Phrases $phrases
     *
     * @return Equipes
     */
    public function setPhrases(\App\Entity\Phrases $phrases = null)
    {
        $this->phrases = $phrases;

        return $this;
    }

    /**
     * Get phrases
     *
     * @return \App\Entity\Phrases
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
    * Set liaison
    *
    * @param \App\Entity\Liaison $liaison
    *
    * @return Phrases
    */
    public function setLiaison(\App\Entity\Liaison $liaison = null)
    {
    $this->liaison = $liaison;

    return $this;
    }

    /**
    * Get liaison
    *
    * @return \App\Entity\Liaison
    */
    public function getLiaison()
    {
    return $this->liaison;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Equipes
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set classement
     *
     * @param integer $classement
     *
     * @return Equipes
     */
    public function setClassement($classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get classement
     *
     * @return integer
     */
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     *
     * @return Equipes
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set prix
     *
     * @param \App\Entity\Prix $prix
     *
     * @return Equipes
     */
    public function setPrix(\App\Entity\Prix $prix = null)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return \App\Entity\Prix
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set infoequipe
     *
     * @param \App\Entity\Equipesadmin $infoequipe
     *
     * @return Equipes
     */
    public function setInfoequipe(\App\Entity\Equipesadmin $infoequipe = null)
    {
        $this->infoequipe = $infoequipe;

        return $this;
    }

    /**
     * Get infoequipe
     *
     * @return \App\Entity\Equipesadmin
     */
    public function getInfoequipe()
    {
        return $this->infoequipe;
    }

    /**
     * @return Collection|Eleves[]
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addEleve(Eleves $eleve): self
    {
        if (!$this->eleves->contains($eleve)) {
            $this->eleves[] = $eleve;
            $eleve->setEquipeleves($this);
        }

        return $this;
    }

    public function removeEleve(Eleves $eleve): self
    {
        if ($this->eleves->contains($eleve)) {
            $this->eleves->removeElement($eleve);
            // set the owning side to null (unless already changed)
            if ($eleve->getEquipeleves() === $this) {
                $eleve->setEquipeleves(null);
            }
        }

        return $this;
    }

    public function getMemoire()
    {
        return $this->memoire;
    }

    public function setMemoire($memoire)
    {
        $this->memoire = $memoire;

        return $this;
    }

    public function addElefe(Eleves $elefe): self
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves[] = $elefe;
            $elefe->setLettreEquipe($this);
        }

        return $this;
    }

    public function removeElefe(Eleves $elefe): self
    {
        if ($this->eleves->contains($elefe)) {
            $this->eleves->removeElement($elefe);
            // set the owning side to null (unless already changed)
            if ($elefe->getLettreEquipe() === $this) {
                $elefe->setLettreEquipe(null);
            }
        }

        return $this;
    }
    
   
}
