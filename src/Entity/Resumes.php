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
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * Resumes
 * @Vich\Uploadable
 * @ORM\Table(name="resumes")
 * @ORM\Entity(repositoryClass="App\Repository\ResumesRepository")
 * 
 */ 

class Resumes 
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
       * @ORM\OneToOne(targetEntity="App\Entity\Equipesadmin")
       * @ORM\JoinColumn(name="equipe_id",  referencedColumnName="id" )
       */
      private $equipe;
      
      /**
        * @ORM\Column(type="string", length=255,  nullable=true)
        * @var string
        */
      private $resume;
     
    /**
     *  
     *  @var File 
     *  @Vich\UploadableField(mapping="resumes", fileNameProperty="resume")
     * 
     */
     private $resumeFile;
    
      
    
    
     /**
       * 
       * 
       * @ORM\Column(type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
    
    
    public function getResumeFile()
    {
        return $this->resumeFile;
    }
    
    public function getResume()
    {
        return $this->resume;
    }
    
    public function setResume($resume)
    {     $this->resume = $resume;
     if (null !== $resume) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }
  /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $resumeFile
     */
    public function setResumeFile(File $resumeFile = null) : void
     {  
         $this->resumeFile=$resumeFile;
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
    
   public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
        return $this;
    }
    
public function personalNamer()    //permet à easyadmin de renonnmer le fichier, ne peut pas être utilisé directement
 {
           
           $edition=$this->getEdition()->getEd();
           
           $equipe=$this->getEquipe();
           $id_equipe=$equipe->getLettre();
           if(!$id_equipe){
           $id_equipe=$equipe->getNumero();
                      }
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
           
           
          
           $fileName=$edition.'-eq-'.$id_equipe.'-resume-'.$nom_equipe;
           
          
           
           return $fileName;
 }
 public function upload($resume)
       {
           // the file property can be empty if the field is not required
           
   
          // we use the original file name here but you should
          // sanitize it at least to avoid any security issues
           //$this->setMemoire($memoire);
           $equipe=$resume->getEquipe();
           $ide_equipe=$equipe->getNumero();
           if ($equipe->getLettre()){
           $ide_equipe=$equipe->getLettre();}
           
           $nom_equipe=$equipe->getTitreProjet();
          // move takes the target directory and target filename as params
           // $file stores the uploaded PDF file
               /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
               $file=$memoire->getMemoireFile();
            $fileUploader= new FileUploader('upload/equipes/resumes');   
           $repertoire=$fileUploader->getTargetDirectory();
           if ($file ) {
           $fileName=$ide_equipe.'-resumé-'.$nom_equipe.'.'.$file->guessExtension();
          // $this->getEquipe().'-memoire-'.$nom_equipe.'.'.$memoire->guessExtension();
           try { $file->move($repertoire
                          ,$fileName
                      );
                           } catch (FileException $e) {
                   // ... handle exception if something happens during file upload
                       }
           $this->setResume($fileName);
           
                           }
         return $this;
   
          // set the path property to the filename where you've saved the file
          //$this->memoire=$this->getMemoire()->getClientOriginalName();
   
          // clean up the file property as you won't need it anymore
          
      }
 
   public function getNom_equipe(){
       $nom_equipe=$this->getDoctrine()
    ->getRepository(Equipesadmin::class)
    -> findByNumero($this->equipe->getNumero())->getTitreProjet();
       
       return $nom_equipe;
       
   }

   public function getUpdatedAt()
   {
       return $this->updatedAt;
   }
    public function setUpdatedAt($date)
   {
      $this->updatedAt=$date;
      return $this;
   }
   
   public function __toString()
    {
        return (string) $this->getResume();
    }
    
}

