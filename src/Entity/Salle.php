<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Salle
 *
 * @ORM\Table(name="salle")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SalleRepository")
 */
class Salle
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
     * @ORM\Column(name="salle", type="string", length=100, nullable=false)
     */
    private $salle;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Session", inversedBy="idSalle")
     * @ORM\JoinTable(name="a_lieu_dans",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_salle", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_session", referencedColumnName="id")
     *   }
     * )
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

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): self
    {
        $this->salle = $salle;

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

}
