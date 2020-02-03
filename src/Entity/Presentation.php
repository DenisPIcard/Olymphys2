<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
 * Fichiersequipes
 * @Vich\Uploadable
 * @ORM\Table(name="Presentation")
 * @ORM\Entity(repositoryClass="App\Repository\PresentationRepository")
 * 
 */



class Presentation //extends BaseMedia
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
       * @ORM\ManyToOne(targetEntity="App\Entity\Equipesadmin")
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id" )
       */
      private $equipe;
      
      /**
        * @ORM\Column(type="string", length=255,  nullable=true)
        * @var string
        */
      private $presentation;
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="presentation", fileNameProperty="presentation")
     *  @Assert\File(
     *          maxSize = "2600000",
     *          mimeTypes = {"application/pdf", "application/x-pdf"},
     *          mimeTypesMessage = "Transfert non effectué ! Déposer un fichier au format pdf de taille maxi 2,5 M"
     *          )
     */
     private $presentationFile;
       
    
     /**
       * 
       * 
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
      public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
        return $this;
    }
    
    public function getPresentationFile()
    {
        return $this->presentationFile;
    }
    
    public function getPresentation()
    {
        return $this->presentation;
    }
  
    public function setPresentation($presentation)
    {   
        $this->presentation = $presentation;
        if ($presentation) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
       
        return $this;
        }
    }

    public function setPresentationFile(File $presentationFile = null)
            
    {  
       
        
        $this->presentationFile=$presentationFile;
       
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
    

    
public function personalNamer()    //permet à easyadmin de renonnmer le fichier, ne peut pas être utilisé directement
 {
           
            $edition=$this->getEdition()->getEd();
           $equipe=$this->getEquipe();
           $lettre_equipe=$equipe->getLettre();
           $nom_equipe=$equipe->getTitreProjet();
           $nom_equipe= str_replace("à","a",$nom_equipe);
           $nom_equipe= str_replace("ù","u",$nom_equipe);
           $nom_equipe= str_replace("è","e",$nom_equipe);
           $nom_equipe= str_replace("é","e",$nom_equipe);
           $nom_equipe= str_replace("ë","e",$nom_equipe);
           $nom_equipe= str_replace("ê","e",$nom_equipe);
            $nom_equipe= str_replace("?"," ",$nom_equipe);
            setLocale(LC_CTYPE,'fr_FR');
           $nom_equipe = iconv('UTF-8','ASCII//TRANSLIT',$nom_equipe);
            //$nom_equipe= str_replace("'","",$nom_equipe);
           //$nom_equipe= str_replace("`","",$nom_equipe);
            
           //$nom_equipe= str_replace("?","",$nom_equipe);
           
               $fileName=$edition.'-eq-'.$lettre_equipe.'- Presentation-'.$nom_equipe;
          
          
           
           return $fileName;
 }
    




   /**
    * Updates the hash value to force the preUpdate and postUpdate events to fire.
    */
   public function refreshUpdated()
   {
      $this->setUpdated(new \DateTime());
   }
    
        
   public function setUpdated($date)
   {
      $this->updated = $date;

        return $this;
   }
   
   public function getUpdatedAt()
   {
       return $this->updatedAt;
   }
    public function getUpdatedannexeAt()
   {
       return $this->updatedannexeAt;
   }
   
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    } 
    
   
   
}

