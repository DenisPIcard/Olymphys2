<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RneRepository")
 */
class Rne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $rne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commune;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $academie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $appellationOfficielle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $boitePostale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $sigle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $denominationPrincipale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acheminement;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=1, nullable=true)
     */
    private $coordonneeX;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=1, nullable=true)
     */
    private $coordonneeY;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRne(): ?string
    {
        return $this->rne;
    }

    public function setRne(string $rne): self
    {
        $this->rne = $rne;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getAcademie(): ?string
    {
        return $this->academie;
    }

    public function setAcademie(?string $academie): self
    {
        $this->academie = $academie;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getAppellationOfficielle(): ?string
    {
        return $this->appellationOfficielle;
    }

    public function setAppellationOfficielle(?string $appellationOfficielle): self
    {
        $this->appellationOfficielle = $appellationOfficielle;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getBoitePostale(): ?string
    {
        return $this->boitePostale;
    }

    public function setBoitePostale(?string $boitePostale): self
    {
        $this->boitePostale = $boitePostale;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getDenominationPrincipale(): ?string
    {
        return $this->denominationPrincipale;
    }

    public function setDenominationPrincipale(?string $denominationPrincipale): self
    {
        $this->denominationPrincipale = $denominationPrincipale;

        return $this;
    }

    public function getAcheminement(): ?string
    {
        return $this->acheminement;
    }

    public function setAcheminement(?string $acheminement): self
    {
        $this->acheminement = $acheminement;

        return $this;
    }

    public function getCoordonneeX(): ?string
    {
        return $this->coordonneeX;
    }

    public function setCoordonneeX(?string $coordonneeX): self
    {
        $this->coordonneeX = $coordonneeX;

        return $this;
    }

    public function getCoordonneeY(): ?string
    {
        return $this->coordonneeY;
    }

    public function setCoordonneeY(?string $coordonneeY): self
    {
        $this->coordonneeY = $coordonneeY;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(?string $nature): self
    {
        $this->nature = $nature;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
