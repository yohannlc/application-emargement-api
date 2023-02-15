<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Etudiant
 *
 * @ORM\Table(name="etudiant", indexes={@ORM\Index(name="etudiant_promo_FK", columns={"promo"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\EtudiantRepository")
 */
class Etudiant
{
    /**
     * @var string
     *
     * @ORM\Column(name="ine", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ine;

    /**
     * @var string
     *
     * @ORM\Column(name="identifiant", type="string", length=15, nullable=false)
     */
    private $identifiant;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=100, nullable=false)
     */
    private $prenom;

    /**
     * @var \Promo
     *
     * @ORM\ManyToOne(targetEntity="Promo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="promo", referencedColumnName="promo")
     * })
     */
    private $promo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Session", inversedBy="ine")
     * @ORM\JoinTable(name="participe",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ine", referencedColumnName="ine")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_session", referencedColumnName="id")
     *   }
     * )
     */
    private $idSession = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Groupe", inversedBy="ine")
     * @ORM\JoinTable(name="fait_partie",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ine", referencedColumnName="ine")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_groupe", referencedColumnName="id")
     *   }
     * )
     */
    private $idGroupe = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idSession = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idGroupe = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIne(): ?string
    {
        return $this->ine;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getIdSession(): Collection
    {
        return $this->idSession;
    }

    public function addIdSession(Session $idSession): self
    {
        if (!$this->idSession->contains($idSession)) {
            $this->idSession->add($idSession);
        }

        return $this;
    }

    public function removeIdSession(Session $idSession): self
    {
        $this->idSession->removeElement($idSession);

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getIdGroupe(): Collection
    {
        return $this->idGroupe;
    }

    public function addIdGroupe(Groupe $idGroupe): self
    {
        if (!$this->idGroupe->contains($idGroupe)) {
            $this->idGroupe->add($idGroupe);
        }

        return $this;
    }

    public function removeIdGroupe(Groupe $idGroupe): self
    {
        $this->idGroupe->removeElement($idGroupe);

        return $this;
    }

}
