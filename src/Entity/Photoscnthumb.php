<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
//use App\Service\FileUploader;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\PropertyNamer;
use App\Entity\Edition ;
 //
//

/**
 * @ORM\Table(name="photoscnthumb")
 * @ORM\Entity(repositoryClass="App\Repository\PhotoscnthumbRepository")
 * @Vich\Uploadable
 */



class Photoscnthumb
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
        * @ORM\Column(type="string", length=255,  nullable=true)
        * @Assert\Unique
        * @var string
        */
      private $photo;
      
      
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="photoscnthumb", fileNameProperty="photo")
     *    
     */
     private $photoFile;
    
    
     
     /**
       * 
       * x
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
    
    
    public function getPhotoFile()
    {
        return $this->photoFile;
    }
    
    public function getPhoto()
    {
        return $this->photo;
    }
    
    public function setPhoto($photo)
    {   
        $this->photo = $photo;
         if ($photo) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTimeImmutable();
       
        //return $this;
        }
    }

    
    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $photoFile
     */
      public function setPhotoFile(?File $photoFile = null) : void
            
    {  
        $this->photoFile=$photoFile;
       
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
       
    }
    
    
   
    public function getId()
    {
        return $this->id;
    }

   
     

    
public function personalNamer()    //permet à vichuploeder et à easyadmin de renommer le fichier, ne peut pas être utilisé directement
 {         $edition='x';
           if ( $this->getEdition()) 
           {
           $edition=$this->getEdition()->getEd(); 
             }
           $equipe=$this->getEquipe();
          
           $lettre_equipe=$equipe->getLettre();
           $nom_equipe=$equipe->getTitreProjet();
           $nom_equipe= str_replace("à","a",$nom_equipe);
           $nom_equipe= str_replace("ù","u",$nom_equipe);
           $nom_equipe= str_replace("è","e",$nom_equipe);
           $nom_equipe= str_replace("é","e",$nom_equipe);
           $nom_equipe= str_replace("ë","e",$nom_equipe);
           $nom_equipe= str_replace("ê","e",$nom_equipe);
            setLocale(LC_CTYPE,'fr_FR');
           
           
           $nom_equipe = iconv('UTF-8','ASCII//TRANSLIT',$nom_equipe);
           //$nom_equipe= str_replace("'","",$nom_equipe);
           //$nom_equipe= str_replace("`","",$nom_equipe);
            
           //$nom_equipe= str_replace("?","",$nom_equipe);     
           $fileName=$edition.'-CN-eq-'.$lettre_equipe.'-'.$nom_equipe.'.'.uniqid();
               
          
           
           return $fileName;
 }
    
    
 



   /**
    * Updates the hash value to force the preUpdate and postUpdate events to fire.
    */
   public function refreshUpdated()
   {
      $this->setUpdated(new \DateTime());
   }
    
        
   public function setUpdatedAt($date)
   {
      $this->updatedAt = $date;

        return $this;
   }
   public function getNom_equipe($lettre){
       $nom_equipe=$this->getDoctrine()
    ->getRepository(Totalequipes::class)
    -> getTotEquipesNom($lettre);
       
       return $nom_equipe;
       
   }

   public function getUpdatedAt()
   {
       return $this->updatedAt;
   }
   
   
    
}

