<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="session", indexes={@ORM\Index(name="session_matiere_FK", columns={"id_matiere"}), @ORM\Index(name="session_type0_FK", columns={"type"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_debut", type="time", nullable=false)
     */
    private $heureDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heure_fin", type="time", nullable=false)
     */
    private $heureFin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \Matiere
     *
     * @ORM\ManyToOne(targetEntity="Matiere")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_matiere", referencedColumnName="id")
     * })
     */
    private $idMatiere;

    /**
     * @var \Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="type")
     * })
     */
    private $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Salle", mappedBy="idSession")
     */
    private $idSalle = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Staff", inversedBy="idSession")
     * @ORM\JoinTable(name="anime",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_session", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_staff", referencedColumnName="id")
     *   }
     * )
     */
    private $idStaff = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Groupe", inversedBy="idSession")
     * @ORM\JoinTable(name="est_inscrit",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_session", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_groupe", referencedColumnName="id")
     *   }
     * )
     */
    private $idGroupe = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Etudiant", mappedBy="idSession")
     */
    private $ine = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idSalle = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idStaff = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idGroupe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ine = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(\DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIdMatiere(): ?Matiere
    {
        return $this->idMatiere;
    }

    public function setIdMatiere(?Matiere $idMatiere): self
    {
        $this->idMatiere = $idMatiere;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getIdSalle(): Collection
    {
        return $this->idSalle;
    }

    public function addIdSalle(Salle $idSalle): self
    {
        if (!$this->idSalle->contains($idSalle)) {
            $this->idSalle->add($idSalle);
            $idSalle->addIdSession($this);
        }

        return $this;
    }

    public function removeIdSalle(Salle $idSalle): self
    {
        if ($this->idSalle->removeElement($idSalle)) {
            $idSalle->removeIdSession($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Staff>
     */
    public function getIdStaff(): Collection
    {
        return $this->idStaff;
    }

    public function addIdStaff(Staff $idStaff): self
    {
        if (!$this->idStaff->contains($idStaff)) {
            $this->idStaff->add($idStaff);
        }

        return $this;
    }

    public function removeIdStaff(Staff $idStaff): self
    {
        $this->idStaff->removeElement($idStaff);

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

    /**
     * @return Collection<int, Etudiant>
     */
    public function getIne(): Collection
    {
        return $this->ine;
    }

    public function addIne(Etudiant $ine): self
    {
        if (!$this->ine->contains($ine)) {
            $this->ine->add($ine);
            $ine->addIdSession($this);
        }

        return $this;
    }

    public function removeIne(Etudiant $ine): self
    {
        if ($this->ine->removeElement($ine)) {
            $ine->removeIdSession($this);
        }

        return $this;
    }

}
