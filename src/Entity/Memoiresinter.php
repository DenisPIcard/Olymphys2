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
 * Memoiresinter
 * @Vich\Uploadable
 * @ORM\Table(name="memoiresinter")
 * @ORM\Entity(repositoryClass="App\Repository\MemoiresinterRepository")
 * 
 */ 

class Memoiresinter 
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
     *  @Vich\UploadableField(mapping="memoiresinter", fileNameProperty="memoire")
     * 
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
       * @ORM\Column( type="datetime", nullable=true)
       * @var \DateTime
       */
    private $updatedAt;
    
    
    
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
         if (null !== $memoire) {
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $memoireFile
     */
    public function setMemoireFile(File $memoireFile = null) : void
     {  
         $this->memoireFile=$memoireFile;
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
       
        
        
    }
    
    /**
     * //Get Id
     * //@Return int
     */
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
           $num_equipe=$equipe->getNumero();
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
           
           
           if ($this->getAnnexe()==false)
               {
                $fileName=$edition.'-eq-'.$num_equipe.'-memoire-'.$nom_equipe;
                }
           else 
           {
               $fileName=$edition.'-eq-'.$num_equipe.'-Annexe';
           }
          
           
           return $fileName;
 }
    
   public function getNom_equipe(){
       $nom_equipe=$this->getDoctrine()
    ->getRepository(Equipesadmin::class)
    -> findByNumero($this->equipe->getNumero())->getTitreProjet();
       
       return $nom_equipe;
       
   }

    /**
     * Get updated_At
     *
     * @return \DateTime
     */  
   public function getUpdatedAt()
   {
       return $this->updatedAt;
   }
   
   
    public function setUpdatedAt($date)
   {
      $this->updatedAt=$date;
      return $this;
   }
   
   
    public function getAnnexe()
   {
       return $this->annexe;
   }
    public function setAnnexe($annexe)
   {
      $this->annexe=$annexe;
   }
   public function __toString()
    {
        return (string) $this->getMemoire();
    }
       
   
}

