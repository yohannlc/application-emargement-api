<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Staff
 *
 * @ORM\Table(name="staff")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 */
class Staff
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
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false)
     */
    private $isAdmin;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="idStaff")
     */
    private $idSession = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idSession = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

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
            $idSession->addIdStaff($this);
        }

        return $this;
    }

    public function removeIdSession(Session $idSession): self
    {
        if ($this->idSession->removeElement($idSession)) {
            $idSession->removeIdStaff($this);
        }

        return $this;
    }

}
