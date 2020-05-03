<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Jures
 *
 * @ORM\Table(name="jures")
 * @ORM\Entity(repositoryClass="App\Repository\JuresRepository")
 */
class Jures
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
     * @ORM\Column(name="prenomJure", type="string", length=255)
     */
    private $prenomJure;

    /**
     * @var string
     *
     * @ORM\Column(name="nomJure", type="string", length=255)
     */
    private $nomJure;

    /**
     * @var string
     *
     * @ORM\Column(name="initialesJure", type="string", length=255)
     */
    private $initialesJure;

    /**
     * @var int
     *
     * @ORM\Column(name="A", type="smallint", nullable=true)
     */
    private $a;

    /**
     * @var int
     *
     * @ORM\Column(name="B", type="smallint", nullable=true)
     */
    private $b;

    /**
     * @var int
     *
     * @ORM\Column(name="C", type="smallint", nullable=true)
     */
    private $c;

    /**
     * @var int
     *
     * @ORM\Column(name="D", type="smallint", nullable=true)
     */
    private $d;

    /**
     * @var int
     *
     * @ORM\Column(name="E", type="smallint", nullable=true)
     */
    private $e;

    /**
     * @var int
     *
     * @ORM\Column(name="F", type="smallint", nullable=true)
     */
    private $f;

    /**
     * @var int
     *
     * @ORM\Column(name="G", type="smallint", nullable=true)
     */
    private $g;

    /**
     * @var int
     *
     * @ORM\Column(name="H", type="smallint", nullable=true)
     */
    private $h;

    /**
     * @var int
     *
     * @ORM\Column(name="I", type="smallint", nullable=true)
     */
    private $i;

    /**
     * @var int
     *
     * @ORM\Column(name="J", type="smallint", nullable=true)
     */
    private $j;

    /**
     * @var int
     *
     * @ORM\Column(name="K", type="smallint", nullable=true)
     */
    private $k;

    /**
     * @var int
     *
     * @ORM\Column(name="L", type="smallint", nullable=true)
     */
    private $l;

    /**
     * @var int
     *
     * @ORM\Column(name="M", type="smallint", nullable=true)
     */
    private $m;

    /**
     * @var int
     *
     * @ORM\Column(name="N", type="smallint", nullable=true)
     */
    private $n;

    /**
     * @var int
     *
     * @ORM\Column(name="O", type="smallint", nullable=true)
     */
    private $o;

    /**
     * @var int
     *
     * @ORM\Column(name="P", type="smallint", nullable=true)
     */
    private $p;

    /**
     * @var int
     *
     * @ORM\Column(name="Q", type="smallint", nullable=true)
     */
    private $q;

    /**
     * @var int
     *
     * @ORM\Column(name="R", type="smallint", nullable=true)
     */
    private $r;

    /**
     * @var int
     *
     * @ORM\Column(name="S", type="smallint", nullable=true)
     */
    private $s;

    /**
     * @var int
     *
     * @ORM\Column(name="T", type="smallint", nullable=true)
     */
    private $t;

    /**
     * @var int
     *
     * @ORM\Column(name="U", type="smallint", nullable=true)
     */
    private $u;

    /**
     * @var int
     *
     * @ORM\Column(name="V", type="smallint", nullable=true)
     */
    private $v;

    /**
     * @var int
     *
     * @ORM\Column(name="W", type="smallint", nullable=true)
     */
    private $w;

    /**
     * @var int
     *
     * @ORM\Column(name="X", type="smallint", nullable=true)
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="Y", type="smallint", nullable=true)
     */
    private $y;

    /**
     * @var int
     *
     * @ORM\Column(name="Z", type="smallint", nullable=true)
     */
    private $z;

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="jure")
     */
    private $notesj;  


 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prenomJure
     *
     * @param string $prenomJure
     *
     * @return Jures
     */
    public function setPrenomJure($prenomJure)
    {
        $this->prenomJure = $prenomJure;

        return $this;
    }

    /**
     * Get prenomJure
     *
     * @return string
     */
    public function getPrenomJure()
    {
        return $this->prenomJure;
    }

    /**
     * Set nomJure
     *
     * @param string $nomJure
     *
     * @return Jures
     */
    public function setNomJure($nomJure)
    {
        $this->nomJure = $nomJure;

        return $this;
    }

    /**
     * Get nomJure
     *
     * @return string
     */
    public function getNomJure()
    {
        return $this->nomJure;
    }

    /**
     * Set initialesJure
     *
     * @param string $initialesJure
     *
     * @return Jures
     */
    public function setInitialesJure($initialesJure)
    {
        $this->initialesJure = $initialesJure;

        return $this;
    }

    /**
     * Get initialesJure
     *
     * @return string
     */
    public function getInitialesJure()
    {
        return $this->initialesJure;
    }

    /**
     * Set a
     *
     * @param integer $a
     *
     * @return Jures
     */
    public function setA($a)
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get a
     *
     * @return int
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * Set b
     *
     * @param integer $b
     *
     * @return Jures
     */
    public function setB($b)
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Get b
     *
     * @return int
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * Set c
     *
     * @param integer $c
     *
     * @return Jures
     */
    public function setC($c)
    {
        $this->c = $c;

        return $this;
    }

    /**
     * Get c
     *
     * @return int
     */
    public function getC()
    {
        return $this->c;
    }

    /**
     * Set d
     *
     * @param integer $d
     *
     * @return Jures
     */
    public function setD($d)
    {
        $this->d = $d;

        return $this;
    }

    /**
     * Get d
     *
     * @return int
     */
    public function getD()
    {
        return $this->d;
    }

    /**
     * Set e
     *
     * @param integer $e
     *
     * @return Jures
     */
    public function setE($e)
    {
        $this->e = $e;

        return $this;
    }

    /**
     * Get e
     *
     * @return int
     */
    public function getE()
    {
        return $this->e;
    }

    /**
     * Set f
     *
     * @param integer $f
     *
     * @return Jures
     */
    public function setF($f)
    {
        $this->f = $f;

        return $this;
    }

    /**
     * Get f
     *
     * @return int
     */
    public function getF()
    {
        return $this->f;
    }

    /**
     * Set g
     *
     * @param integer $g
     *
     * @return Jures
     */
    public function setG($g)
    {
        $this->g = $g;

        return $this;
    }

    /**
     * Get g
     *
     * @return int
     */
    public function getG()
    {
        return $this->g;
    }

    /**
     * Set h
     *
     * @param integer $h
     *
     * @return Jures
     */
    public function setH($h)
    {
        $this->h = $h;

        return $this;
    }

    /**
     * Get h
     *
     * @return int
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * Set i
     *
     * @param integer $i
     *
     * @return Jures
     */
    public function setI($i)
    {
        $this->i = $i;

        return $this;
    }

    /**
     * Get i
     *
     * @return int
     */
    public function getI()
    {
        return $this->i;
    }

    /**
     * Set j
     *
     * @param integer $j
     *
     * @return Jures
     */
    public function setJ($j)
    {
        $this->j = $j;

        return $this;
    }

    /**
     * Get j
     *
     * @return int
     */
    public function getJ()
    {
        return $this->j;
    }

    /**
     * Set k
     *
     * @param integer $k
     *
     * @return Jures
     */
    public function setK($k)
    {
        $this->k = $k;

        return $this;
    }

    /**
     * Get k
     *
     * @return int
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * Set l
     *
     * @param integer $l
     *
     * @return Jures
     */
    public function setL($l)
    {
        $this->l = $l;

        return $this;
    }

    /**
     * Get l
     *
     * @return int
     */
    public function getL()
    {
        return $this->l;
    }

    /**
     * Set m
     *
     * @param integer $m
     *
     * @return Jures
     */
    public function setM($m)
    {
        $this->m = $m;

        return $this;
    }

    /**
     * Get m
     *
     * @return int
     */
    public function getM()
    {
        return $this->m;
    }

    /**
     * Set n
     *
     * @param integer $n
     *
     * @return Jures
     */
    public function setN($n)
    {
        $this->n = $n;

        return $this;
    }

    /**
     * Get n
     *
     * @return int
     */
    public function getN()
    {
        return $this->n;
    }

    /**
     * Set o
     *
     * @param integer $o
     *
     * @return Jures
     */
    public function setO($o)
    {
        $this->o = $o;

        return $this;
    }

    /**
     * Get o
     *
     * @return int
     */
    public function getO()
    {
        return $this->o;
    }

    /**
     * Set p
     *
     * @param integer $p
     *
     * @return Jures
     */
    public function setP($p)
    {
        $this->p = $p;

        return $this;
    }

    /**
     * Get p
     *
     * @return int
     */
    public function getP()
    {
        return $this->p;
    }

    /**
     * Set q
     *
     * @param integer $q
     *
     * @return Jures
     */
    public function setQ($q)
    {
        $this->q = $q;

        return $this;
    }

    /**
     * Get q
     *
     * @return int
     */
    public function getQ()
    {
        return $this->q;
    }

    /**
     * Set r
     *
     * @param integer $r
     *
     * @return Jures
     */
    public function setR($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Get r
     *
     * @return int
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * Set s
     *
     * @param integer $s
     *
     * @return Jures
     */
    public function setS($s)
    {
        $this->s = $s;

        return $this;
    }

    /**
     * Get s
     *
     * @return int
     */
    public function getS()
    {
        return $this->s;
    }

    /**
     * Set t
     *
     * @param integer $t
     *
     * @return Jures
     */
    public function setT($t)
    {
        $this->t = $t;

        return $this;
    }

    /**
     * Get t
     *
     * @return int
     */
    public function getT()
    {
        return $this->t;
    }

    /**
     * Set u
     *
     * @param integer $u
     *
     * @return Jures
     */
    public function setU($u)
    {
        $this->u = $u;

        return $this;
    }

    /**
     * Get u
     *
     * @return int
     */
    public function getU()
    {
        return $this->u;
    }

    /**
     * Set v
     *
     * @param integer $v
     *
     * @return Jures
     */
    public function setV($v)
    {
        $this->v = $v;

        return $this;
    }

    /**
     * Get v
     *
     * @return int
     */
    public function getV()
    {
        return $this->v;
    }

    /**
     * Set w
     *
     * @param integer $w
     *
     * @return Jures
     */
    public function setW($w)
    {
        $this->w = $w;

        return $this;
    }

    /**
     * Get w
     *
     * @return int
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Set x
     *
     * @param integer $x
     *
     * @return Jures
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param integer $y
     *
     * @return Jures
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set z
     *
     * @param integer $z
     *
     * @return Jures
     */
    public function setZ($z)
    {
        $this->z = $z;

        return $this;
    }

    /**
     * Get z
     *
     * @return int
     */
    public function getZ()
    {
        return $this->z;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notesj = new \Doctrine\Common\Collections\ArrayCollection();
    }

     public function getAttributions()
    {
        $attribution=array();

        foreach (range('A','Z') as $i)
        {
        // On récupère le nom du getter correspondant à l'attribut.
        $method = 'get'.ucfirst($i);
        

        // Si le getter correspondant existe.
        if (method_exists($this, $method))
        {
        // On appelle le setter.
        $statut = $this->$method();
            if($statut == 1)
            {
                $attribution[$i]=1;
            } 
            elseif (is_int($statut)) {
                $attribution[$i]=0;
            }
        }

        }
        return $attribution;

    }
       /**
     * Add notesj
     *
     * @param \App\Entity\Notes $notesj
     *
     * @return Jures
     */
    public function addNotesj(\App\Entity\Notes $notesj)
    {
        $this->notesj[] = $notesj;

        //On relie l'équipe à "une ligne note"
        $notesj->setJure($this);

        return $this;
    }

    /**
     * Remove notesj
     *
     * @param \App\Entity\Notes $notesj
     */
    public function removeNotesj(\App\Entity\Notes $notesj)
    {
        $this->notess->removeElement($notesj);
    }

    /**
     * Get notesj
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotesj()
    {
        return $this->notesj;
    }
public function getNom()
    {
        return $this->getNomJure().' '.$this->getPrenomJure();
    }

}
