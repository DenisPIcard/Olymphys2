<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * Centrescia
 * 
 * @ORM\Table(name="centrescia")
 * @ORM\Entity(repositoryClass="App\Repository\CentresciaRepository")
 * 
 */

class Centrescia
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
        * @ORM\Column(type="string", length=255, nullable = true)
        * @var string
        */
      private $centre;
      
      
      /**
        * @ORM\ManyToOne(targetEntity="App\Entity\Edition")
         * @ORM\JoinColumn(name="id_edition",  referencedColumnName="id" )
        */
      private $edition;
       
      /**
        * @ORM\ManyToOne(targetEntity="App\Entity\User")
         * @ORM\JoinColumn(name="id_orga1",  referencedColumnName="id" )
        */              
      private $orga1;
      /**
        * @ORM\ManyToOne(targetEntity="App\Entity\User")
         * @ORM\JoinColumn(name="id_orga2",  referencedColumnName="id" )
        */
      private $orga2;
      
      /**
        * @ORM\ManyToOne(targetEntity="App\Entity\User")
         * @ORM\JoinColumn(name="id_jurycia",  referencedColumnName="id" )
        */
      private $jurycia;
      
      
      
      
      
      public function getId()
        {
            return $this->id;
        }
    
     public function getCentre()
    {
        return $this->centre;
    }
    
    public function setCentre($centre)
    {
        $this->centre=$centre;
    }

    public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition)
    {
        $this->edition = $edition;

        return $this;
    }

    public function getOrga1()
    {
        return $this->orga1;
    }

    public function setOrga1( $orga1)
    {
        $this->orga1 = $orga1;

        return $this;
    }

    public function getOrga2()
    {
        return $this->orga2;
    }

    public function setOrga2($orga2)
    {
        $this->orga2 = $orga2;

        return $this;
    }

    public function getJurycia()
    {
        return $this->jurycia;
    }

    public function setJurycia($jurycia)
    {
        $this->jurycia = $jurycia;

        return $this;
    }  
      
}