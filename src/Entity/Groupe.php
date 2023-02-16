<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\GroupeRepository")
 */
class Groupe
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
     * @var string
     *
     * @ORM\Column(name="groupe", type="string", length=100, nullable=false)
     */
    private $groupe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="idGroupe")
     */
    private $idSession = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Etudiant", mappedBy="idGroupe")
     */
    private $ine = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idSession = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ine = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): self
    {
        $this->groupe = $groupe;

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
            $idSession->addIdGroupe($this);
        }

        return $this;
    }

    public function removeIdSession(Session $idSession): self
    {
        if ($this->idSession->removeElement($idSession)) {
            $idSession->removeIdGroupe($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getIne(): Collection
    {
        return $this->ine;
    }

    public function addEtudiant(Etudiant $ine): self
    {
        if (!$this->ine->contains($ine)) {
            $this->ine->add($ine);
            $ine->addIdGroupe($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $ine): self
    {
        if ($this->ine->removeElement($ine)) {
            $ine->removeIdGroupe($this);
        }

        return $this;
    }

    public function hasEtudiant(Etudiant $etudiant): bool
    {
        return $this->ine->contains($etudiant);
    }

}
