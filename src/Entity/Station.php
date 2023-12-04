<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de la station ne peut pas être vide.')]
    private ?string $nom_s = null;

    #[ORM\Column(length: 255)]
    private ?string $emplacement_s = null;

    #[ORM\Column(length: 255)]
    private ?string $description_s = null;

    #[ORM\OneToMany(mappedBy: 'stations', targetEntity: Maintenance::class)]
    private Collection $maintenances;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    private ?int $dislikes = null;

    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomS(): ?string
    {
        return $this->nom_s;
    }

    public function setNomS(string $nom_s): static
    {
        $this->nom_s = $nom_s;

        return $this;
    }

    public function getEmplacementS(): ?string
    {
        return $this->emplacement_s;
    }

    public function setEmplacementS(string $emplacement_s): static
    {
        $this->emplacement_s = $emplacement_s;

        return $this;
    }

    public function getDescriptionS(): ?string
    {
        return $this->description_s;
    }

    public function setDescriptionS(string $description_s): static
    {
        $this->description_s = $description_s;

        return $this;
    }

    /**
     * @return Collection<int, Maintenance>
     */
    public function getMaintenances(): Collection
    {
        return $this->maintenances;
    }

    public function addMaintenance(Maintenance $maintenance): static
    {
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances->add($maintenance);
            $maintenance->setStations($this);
        }

        return $this;
    }

    public function removeMaintenance(Maintenance $maintenance): static
    {
        if ($this->maintenances->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getStations() === $this) {
                $maintenance->setStations(null);
            }
        }

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): static
    {
        $this->dislikes = $dislikes;

        return $this;
    }
}