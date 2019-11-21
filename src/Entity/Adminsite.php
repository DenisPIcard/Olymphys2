<?php
// src/Entity/Author.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adminsite
 *
 * @ORM\Table(name="adminsite")
 * @ORM\Entity(repositoryClass="App\Repository\AdminsiteRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class Adminsite
{   /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
      /**
       * @var string
       * @ORM\Column(name="session", type="string", nullable=true)
       */
    private $session;    
    
      /**
        * @var \datetime
        * @ORM\Column(name="datelimite_cia", type="datetime", nullable=true)
        */    
        protected $datelimcia;
    
       /**
        * @var \datetime
        *  @ORM\Column(name="datelimite_nat", type="datetime",nullable=true)
        */    
        protected $datelimnat;
    
       
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getSession()
    {
        return $this->session;
    }
    
      public function setSession($session)
    {
        $this->session = $session;
    }
    
    
    
    public function setDatelimcia($Date)
    {
        $this->datelimcia = $Date;
    }

    public function getDatelimcia()
    {
        return $this->datelimcia;
    }
    
     public function setDatelimnat($Date)
    {
        $this->datelimnat = $Date;
    }

    public function getDatelimnat()
    {
        return $this->datelimnat;
    }
    
   
    
    
}

