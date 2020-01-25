<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Equipesadmin
 * @Vich\Uploadable
 * @ORM\Table(name="equipesadmin")
 * @ORM\Entity(repositoryClass="App\Repository\EquipesadminRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Equipesadmin
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
     * @ORM\Column(name="lettre", type="string", length=1, unique=true,nullable= true)
     */
    private $lettre;
     
     /**
     * @var int
     *
     * @ORM\Column(name="numero", type="smallint",unique=true,nullable=true)
     */
    private $numero; 
          
     /**
     * @var string
     * //@ORM\Column(name="centre", type="string", nullable=true)
     * @ORM\ManyToOne(targetEntity="App\Entity\Centrescia")
     * @ORM\JoinColumn(name ="centre_id", referencedColumnName = "id", nullable=true)
     */
    private $centre; 
    
    /**
     * @var boolean
     * @ORM\Column(name="selectionnee", type="boolean", nullable=true)
     */
    private $selectionnee;

    /**
     * @var string
     *
     * @ORM\Column(name="titreProjet", type="string", length=255, unique=true, nullable=true)
     */
    private $titreProjet;

     // /**
   //  * @ORM\OneToOne(targetEntity="App\Entity\Resumes", mappedBy="id",  cascade={"remove"})
   //  * 
    // */
    //private $resume;    
  
    
    
    ///**
     //* @ORM\OneToOne(targetEntity="App\Entity\Fichessecur", mappedBy="id")
     //* @ORM\JoinColumn(name="fichesecur_id")
     //*/
    //private $fichesecur;  
    
   /**
     * @var string
     *
     * @ORM\Column(name="nom_lycee", type="string", length=255, nullable=true)
     */
    private $nomLycee;

    /**
     * @var string
     *
     * @ORM\Column(name="denomination_lycee", type="string", length=255, nullable=true)
     */
    private $denominationLycee;

    /**
     * @var string
     *
     * @ORM\Column(name="lycee_localite", type="string", length=255, nullable=true)
     */
    private $lyceeLocalite;

    /**
     * @var string
     *
     * @ORM\Column(name="lycee_academie", type="string", length=255, nullable=true)
     */
    private $lyceeAcademie;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof1", type="string", length=255, nullable=true)
     */
    private $prenomProf1;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof1", type="string", length=255, nullable=true)
     */
    private $nomProf1;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_prof2", type="string", length=255, nullable=true)
     */
    private $prenomProf2;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof2", type="string", length=255, nullable=true)
     */
    private $nomProf2; 
    
    /**
     * @var string
     * @ORM\Column(name="rne", type="string", length=255, nullable=true)
     */
    private $rne;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="id")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idProf1;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="id")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idProf2; 
    
    
   
    
  
    public function __toString(): string
        {
           return $this->centre;
           
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
     * Set titreProjetinter
     *
     * @param string $titreProjetinter
     *
     * @return Equipesinter
     */
    public function setTitreProjet($titreProjet)
    {
        $this->titreProjet = $titreProjet;

        return $this;
    }

    /**
     * Get titreProjetinter
     *
     * @return string
     */
    public function getTitreProjet()
    {
        return $this->titreProjet;
    }

        /**
     * Set numero
     *
     * @param integer $numero
     *
     * @return Equipesinter
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }
    
     /**
     * Set lettre
     *
     * @param string $lettre
     *
     * @return Equipesadmin
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
     * Set centre
     *
     * @param  App\Entity\Centrescia $centre
     *
     * @return Equipesadmin
     */
    public function setCentre(\App\Entity\Centrescia $centre = null)
    {  
           
        //$centre=$centre->getCentre();
         
        $this->centre = $centre;

        return $this;
    }

    /**
     * Get fichesecur
     *
     * @return string
     */
    public function getFichesecur()
    {
        return $this->Fichesecur;
    }
   
    public function setFichesecur($fiche)
    {
        $this->fichesecur = $fiche;

        return $this;
    }
    
     public function getResume()
    {
        return $this->resume;
    }
   
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }
    
    
    

    /**
     * Get centre
     *
     * @return string
     */
    public function getCentre()
    {    
        return $this->centre;
    }
    


    /**
     * Get infoequipe
     *
     * @return \App\Entity\Equipesadmin
     */
    public function getInfoequipe()
    {   
        $nomcentre='';
        $Numero=$this->getNumero();
        If ($centre =$this->getCentre()){
        $nomcentre =$this->getCentre()->getCentre().'-';}
               
        
        $nom_equipe=$this->getTitreProjet() ;
        $ville=$this->getLyceeLocalite();
        
        $infoequipe= $nomcentre.'Eq '.$Numero.' - '.$nom_equipe.'-'.$ville;        
        return $infoequipe;
    }
    public function getInfoequipenat()
    {   
        
        $Lettre=$this->getLettre();
        if ($this->getLettre())
        {
        
        
        $nom_equipe=$this->getTitreProjet() ;
        $ville=$this->getLyceeLocalite();
        
        $infoequipe= 'Eq '.$Lettre.' - '.$nom_equipe.'-'.$ville;        
        return $infoequipe;}
    }
    

    

   
     
  
    public function getSelectionnee()
    {
        return $this->selectionnee;
    }

    public function setSelectionnee($selectionnee)
    {
        $this->selectionnee = $selectionnee;

        return $this;
    }
    /**
     * Set nomLycee
     *
     * @param string $nomLycee
     *
     * @return Totalequipes
     */
    public function setNomLycee($nomLycee)
    {
        $this->nomLycee = $nomLycee;

        return $this;
    }

    /**
     * Get nomLycee
     *
     * @return string
     */
    public function getNomLycee()
    {
        return $this->nomLycee;
    }

    /**
     * Set denominationLycee
     *
     * @param string $denominationLycee
     *
     * @return Totalequipes
     */
    public function setDenominationLycee($denominationLycee)
    {
        $this->denominationLycee = $denominationLycee;

        return $this;
    }

    /**
     * Get denominationLycee
     *
     * @return string
     */
    public function getDenominationLycee()
    {
        return $this->denominationLycee;
    }

    /**
     * Set lyceeLocalite
     *
     * @param string $lyceeLocalite
     *
     * @return Totalequipes
     */
    public function setLyceeLocalite($lyceeLocalite)
    {
        $this->lyceeLocalite = $lyceeLocalite;

        return $this;
    }

    /**
     * Get lyceeLocalite
     *
     * @return string
     */
    public function getLyceeLocalite()
    {
        return $this->lyceeLocalite;
    }

    /**
     * Set lyceeAcademie
     *
     * @param string $lyceeAcademie
     *
     * @return Totalequipes
     */
    public function setLyceeAcademie($lyceeAcademie)
    {
        $this->lyceeAcademie = $lyceeAcademie;

        return $this;
    }

    /**
     * Get lyceeAcademie
     *
     * @return string
     */
    public function getLyceeAcademie()
    {
        return $this->lyceeAcademie;
    }

    /**
     * Set prenomProf1
     *
     * @param string $prenomProf1
     *
     * @return Totalequipes
     */
    public function setPrenomProf1($prenomProf1)
    {
        $this->prenomProf1 = $prenomProf1;

        return $this;
    }

    /**
     * Get prenomProf1
     *
     * @return string
     */
    public function getPrenomProf1()
    {
        return $this->prenomProf1;
    }

    /**
     * Set nomProf1
     *
     * @param string $nomProf1
     *
     * @return Totalequipes
     */
    public function setNomProf1($nomProf1)
    {
        $this->nomProf1 = $nomProf1;

        return $this;
    }

    /**
     * Get nomProf1
     *
     * @return string
     */
    public function getNomProf1()
    {
        return $this->nomProf1;
    }

    /**
     * Set prenomProf2
     *
     * @param string $prenomProf2
     *
     * @return Totalequipes
     */
    public function setPrenomProf2($prenomProf2)
    {
        $this->prenomProf2 = $prenomProf2;

        return $this;
    }

    /**
     * Get prenomProf2
     *
     * @return string
     */
    public function getPrenomProf2()
    {
        return $this->prenomProf2;
    }

    /**
     * Set nomProf2
     *
     * @param string $nomProf2
     *
     * @return Totalequipes
     */
    public function setNomProf2($nomProf2)
    {
        $this->nomProf2 = $nomProf2;

        return $this;
    }

    /**
     * Get nomProf2
     *
     * @return string
     */
    public function getNomProf2()
    {
        return $this->nomProf2;
    }
    /**
     * Get rne
     *
     * @return string
     */
    public function getRne()
    {
       return  $this->rne;
    }
    /**
     * Set rne
     *
     * @param string rne
     *
     * @return Equipesadmin
     */
    public function setRne($rne)
    {
        $this->rne=$rne;
        return $this;
    }
    
   public function getLycee()
   {
       return $this->getDenominationLycee().' '.$this->getNomLycee().' de  '.$this->getLyceeLocalite();
   } 
   public function getProf1()
   {
       
       return $this->getPrenomProf1().' '.$this->getNomProf1();
   }
   public function getProf2()
   {
       
       return $this->getPrenomProf2().' '.$this->getNomProf2();
   }

   public function getIdProf1(): int
   {
       return $this->idProf1;
   }

   public function setIdProf1(int $idProf1): self
   {
       $this->idProf1 = $idProf1;

       return $this;
   }

   public function getIdProf2()
   {
       return $this->idProf2;
   }

   public function setIdProf2(int $idProf2): self
   {
       $this->idProf2 = $idProf2;

       return $this;
   }
   
}
