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
 * Memoires
 * @Vich\Uploadable
 * @ORM\Table(name="memoires")
 * @ORM\Entity(repositoryClass="App\Repository\MemoiresRepository")
 * 
 */



class Memoires //extends BaseMedia
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
      private $memoire;
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="memoires", fileNameProperty="memoire")
     *  @Assert\File(
     *          maxSize = "2600000",
     *          mimeTypes = {"application/pdf", "application/x-pdf"},
     *          mimeTypesMessage = "Transfert non effectué ! Déposer un fichier au format pdf de taille maxi 2,5 M"
     *          )
     */
     private $memoireFile;
    
      /**
       *  
       * @ORM\Column(type="boolean", nullable=true)
       * @var boolean
       */
      private $annexe;
    
    
    
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
    
    public function getMemoireFile()
    {
        return $this->memoireFile;
    }
    
    public function getMemoire()
    {
        return $this->memoire;
    }
    
    public function setMemoire($memoire)
    {   
        $this->memoire = $memoire;
        if ($memoire) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
       
        return $this;
        }
    }

    public function setMemoireFile(File $memoireFile = null)
            
    {  
       
        
        $this->memoireFile=$memoireFile;
       
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
           
           if ($this->getAnnexe()==false)
               {
                $fileName=$edition.'-eq-'.$lettre_equipe.'-memoire-'.$nom_equipe;
                }
           else 
           {
               $fileName=$edition.'-eq-'.$lettre_equipe.'-Annexe';
           }
          
           
           return $fileName;
 }
    
    
 public function upload($memoire)
          {
              // the file property can be empty if the field is not required
              
      
             // we use the original file name here but you should
             // sanitize it at least to avoid any security issues
              //$this->setMemoire($memoire);
              $equipe=$memoire->getEquipe();
              $lettre_equipe=$equipe->getLettre();
              $nom_equipe=$equipe->getTitreProjet();
             // move takes the target directory and target filename as params
              // $file stores the uploaded PDF file
                  /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
                  $file=$memoire->getMemoireFile();
               $fileUploader= new FileUploader('upload/equipes/memoires/nat');   
              $repertoire=$fileUploader->getTargetDirectory();
              if ($file ) {
              $fileName=$lettre_equipe.'-memoire-'.$nom_equipe.'.'.$file->guessExtension();
             // $this->getEquipe().'-memoire-'.$nom_equipe.'.'.$memoire->guessExtension();
              try { $file->move($repertoire
                             ,$fileName
                         );
                              } catch (FileException $e) {
                      // ... handle exception if something happens during file upload
                          }
              $this->setMemoire($fileName);
              
                              }
            return $this;
      
             // set the path property to the filename where you've saved the file
             //$this->memoire=$this->getMemoire()->getClientOriginalName();
      
             // clean up the file property as you won't need it anymore
             
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
    public function getUpdatedannexeAt()
   {
       return $this->updatedannexeAt;
   }
   
    public function getAnnexe()
   {
       return $this->annexe;
   }
    public function setAnnexe($annexe)
   {
      $this->annexe=$annexe;
   }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

