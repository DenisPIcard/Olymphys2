<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\PropertyNamer;
/**
 * Fichessecur
 * @Vich\Uploadable
 * @ORM\Table(name="fichessecur")
 * @ORM\Entity(repositoryClass="App\Repository\FichessecurRepository")
 * 
 */



class Fichessecur
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
       *  
       * @ORM\ManyToOne(targetEntity="App\Entity\Edition")
       * @ORM\JoinColumn(name="edition_id",  referencedColumnName="id" )
       */
      private $edition;
     
      /**
       *  
       * @ORM\OneToOne(targetEntity="App\Entity\Equipesadmin" )
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id" )
       */
      private $equipe;
      
      /**
        * @ORM\Column(type="string", length=255,  nullable=true)
        * @var string
        */
      private $fiche;
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="fichessecur", fileNameProperty="fiche")
     */
     private $ficheFile;
    
     /**
       * 
       * 
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
    
    
    public function getFicheFile()
    {
        return $this->ficheFile;
    }
    
    public function getFiche()
    {
        return $this->fiche;
    }
    
    public function setFiche($fiche)
    {   
        $this->fiche = $fiche;
         if ($fiche) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
       
        return $this;
        }
    }
   /*
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $ficheFile
    */
    public function setFicheFile(File $ficheFile = null) : void
            
    {  
       
        
        $this->ficheFile=$ficheFile;
       
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
       
    }
    
    
   
    public function getId()
    {
        return $this->id;
    }

    public function getEquipe()
    {
        return $this->equipe;
    }

    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
        return $this;
    }
    

    
public function personalNamer()    //permet à Vichuploader de renommer le fichier, ne peut pas être utilisé directement
 {
           
           $edition=$this->getEdition()->getEd();
           $equipe=$this->getEquipe();
          
           $nom_equipe=$equipe->getTitreProjet();
           $nom_equipe= str_replace("à","a",$nom_equipe);
           $nom_equipe= str_replace("ù","u",$nom_equipe);
           $nom_equipe= str_replace("è","e",$nom_equipe);
           $nom_equipe= str_replace("é","e",$nom_equipe);
           $nom_equipe= str_replace("ë","e",$nom_equipe);
           $nom_equipe= str_replace("ê","e",$nom_equipe);
           $nom_equipe= str_replace("'"," ",$nom_equipe);
            setLocale(LC_CTYPE,'fr_FR');
           $nom_equipe = iconv('UTF-8','ASCII//TRANSLIT',$nom_equipe);
           //$nom_equipe= str_replace("'","",$nom_equipe);
           //$nom_equipe= str_replace("`","",$nom_equipe);
           $nom_equipe= str_replace("?","",$nom_equipe);
           $num_equipe=$equipe->getNumero();
           
                     
          if ($equipe->getLettre()){
              $lettre_equipe = $equipe->getLettre();
          $fileName=$edition. '-eq-'.$lettre_equipe.'-Fiche_Securite-'.$nom_equipe;}
           else {
               $fileName=$edition.'-eq-'.$num_equipe.'-Fiche_Securite-'.$nom_equipe;
           }
           
           return $fileName;
 }
    
    
  
    
        
   public function setUpdatedAt($date)
   {
      $this->updatedAt = $date;

        return $this;
   }
   public function getNom_equipe($numero){
       $nom_equipe=$this->getEquipe()->getTitreProjet();
       
       return $nom_equipe;
       
   }
    /**
     * Get updatedAt
     *
     * @return updatedAt
     */
   public function getUpdatedAt()
   {   
       return $this->updatedAt;
   }
   
    public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
        return $this;
    }
    public function __toString()
    {
        return (string) $this->getFiche();
    }
}

