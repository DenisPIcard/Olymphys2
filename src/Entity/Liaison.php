<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Liaison
 *
 * @ORM\Table(name="liaison")
 * @ORM\Entity(repositoryClass="App\Repository\LiaisonRepository")
 */
class Liaison
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
     * @ORM\Column(name="liaison", type="string", length=255, nullable=true)
     */
    private $liaison;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set liaison
     *
     * @param string $liaison
     *
     * @return Liaison
     */
    public function setLiaison($liaison)
    {
        $this->liaison = $liaison;

        return $this;
    }

    /**
     * Get liaison
     *
     * @return string
     */
    public function getLiaison()
    {
        return $this->liaison;
    }
}
