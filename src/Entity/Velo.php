<?php

namespace App\Entity;

use App\Repository\VeloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: VeloRepository::class)]

class Velo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"modele is required")]
    private ?string $modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"etat is required")]
    private ?string $etat = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:"prix is required")]
    private ?int $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:"id station is required")]
    private ?int $station = null;

    #[ORM\OneToMany(mappedBy: 'velo', targetEntity: Location::class)]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }


    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        // Modele validation
        $metadata->addPropertyConstraint('modele', new Assert\NotBlank([
            'message' => 'modele is required',
        ]));

        // Etat validation
        $metadata->addPropertyConstraint('etat', new Assert\NotBlank([
            'message' => 'etat is required',
        ]));

        // Prix validation
        $metadata->addPropertyConstraint('prix', new Assert\NotBlank([
            'message' => 'prix is required',
        ]));
    }

    // Add validation annotations based on your requirements
    // For example, use Symfony's Assert component for validation
    public function __toString()
{
    return $this->modele; // Assuming 'modele' is a property in your Velo class
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getStation(): ?int
    {
        return $this->station;
    }

    public function setStation(?int $station): static
    {
        $this->station = $station;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): static
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setVelo($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getVelo() === $this) {
                $location->setVelo(null);
            }
        }

        return $this;
    }
}