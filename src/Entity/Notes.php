<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notes
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="App\Repository\NotesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notes
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
     * @var int
     *
     * @ORM\Column(name="exper", type="smallint")
     */
    private $exper;

    /**
     * @var int
     *
     * @ORM\Column(name="demarche", type="smallint")
     */
    private $demarche;

    /**
     * @var int
     *
     * @ORM\Column(name="oral", type="smallint")
     */
    private $oral;

    /**
     * @var int
     *
     * @ORM\Column(name="origin", type="smallint")
     */
    private $origin;

    /**
     * @var int
     *
     * @ORM\Column(name="Wgroupe", type="smallint")
     */
    private $wgroupe;

    /**
     * @var int
     *
     * @ORM\Column(name="ecrit", type="smallint", nullable=true)
     */
    private $ecrit;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Equipes", inversedBy="notess")
    * @ORM\JoinColumn(nullable=false)
    */
    private $equipe;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Jures", inversedBy="notesj")
    * @ORM\JoinColumn(nullable=false)
    */
    private $jure;

    /**
    * @ORM\PrePersist
    */ 
    public function increase()
    {
        $this->getEquipe()->increaseNbNotes();
    }

    // les constantes de classe 
    //const PAS_NOTE = 0; // état initial de toutes les notes 
    const NE_PAS_NOTER = 0; // pour les écrits.... 
    const INSUFFISANT = 1; 
    const MOYEN = 2;
    const BIEN = 3; 
    const EXCELLENT = 4;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set exper
     *
     * @param integer $exper
     *
     * @return Notes
     */
    public function setExper($exper)
    {
        $this->exper = $exper;

        return $this;
    }

    /**
     * Get exper
     *
     * @return int
     */
    public function getExper()
    {
        return $this->exper;
    }

    /**
     * Set demarche
     *
     * @param integer $demarche
     *
     * @return Notes
     */
    public function setDemarche($demarche)
    {
        $this->demarche = $demarche;

        return $this;
    }

    /**
     * Get demarche
     *
     * @return int
     */
    public function getDemarche()
    {
        return $this->demarche;
    }

    /**
     * Set oral
     *
     * @param string $oral
     *
     * @return Notes
     */
    public function setOral($oral)
    {
        $this->oral = $oral;

        return $this;
    }

    /**
     * Get oral
     *
     * @return string
     */
    public function getOral()
    {
        return $this->oral;
    }

    /**
     * Set origin
     *
     * @param integer $origin
     *
     * @return Notes
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return int
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set wgroupe
     *
     * @param integer $wgroupe
     *
     * @return Notes
     */
    public function setWgroupe($wgroupe)
    {
        $this->wgroupe = $wgroupe;

        return $this;
    }

    /**
     * Get wgroupe
     *
     * @return int
     */
    public function getWgroupe()
    {
        return $this->wgroupe;
    }

    /**
     * Set ecrit
     *
     * @param integer $ecrit
     *
     * @return Notes
     */
    public function setEcrit($ecrit)
    {
        $this->ecrit = $ecrit;

        return $this;
    }

    /**
     * Get ecrit
     *
     * @return int
     */
    public function getEcrit()
    {
        return $this->ecrit;
    }

// Les attributs calculés 

    public function getPoints()
    {
        $points = $this->getExper()*10 + $this->getDemarche()*10 + $this->getOral()*12.5 + $this->getOrigin()*12.5 + $this->getWgroupe()*5; 
        return $points;
    }

    public function getSousTotal()
    {
        $points = $this->getExper()*10 + $this->getDemarche()*10 + $this->getOral()*12.5 + $this->getOrigin()*12.5 + $this->getWgroupe()*5 + $this->getEcrit()*5; 
        return $points;
    }

    /**
     * Set equipe
     *
     * @param \App\Entity\Equipes $equipe
     *
     * @return Notes
     */
    public function setEquipe(Equipes $equipe)
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * Get equipe
     *
     * @return App\Entity\Equipes
     */
    public function getEquipe()
    {
        return $this->equipe;
    }

    /**
     * Set jure
     *
     * @param App\Entity\Jures $jure
     *
     * @return Notes
     */
    public function setJure(Jures $jure)
    {
        $this->jure = $jure;

        return $this;
    }

    /**
     * Get jure
     *
     * @return App\Entity\Jures
     */
    public function getJure()
    {
        return $this->jure;
    }
}
