<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Desk
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['reservation:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['reservation:read'])]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $localisation;

    #[ORM\Column(type: 'integer')]
    private $capacite;

    #[ORM\Column(type: 'string', enumType: \App\Enum\BureauType::class)]
    private $type;

    #[ORM\OneToMany(targetEntity: 'Reservation', mappedBy: 'bureau')]
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
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

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getType(): ?\App\Enum\BureauType
    {
        return $this->type;
    }

    public function setType(\App\Enum\BureauType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }
} 