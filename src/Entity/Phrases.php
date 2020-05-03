<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Phrases
 *
 * @ORM\Table(name="phrases")
 * @ORM\Entity(repositoryClass="App\Repository\PhrasesRepository")
 */
class Phrases
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
     * @var text
     *
     * @ORM\Column(name="phrase", type="text", nullable=true)
     * @Assert\Length(min=1, minMessage="La phrase amusante doit contenir au moins {{ limit }} caractère. ")
     */
    private $phrase;

    /**
     * @var text
     *
     * @ORM\Column(name="prix", type="text", nullable=true)
     * @Assert\Length(min=1, minMessage="L'intitulé du prix amusant doit contenir au moins {{ limit }} caractère. ")
     */
    private $prix;

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
     * Set phrase
     *
     * @param string $phrase
     *
     * @return Phrases
     */
    public function setPhrase($phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get phrase
     *
     * @return string
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Phrases
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }

}
