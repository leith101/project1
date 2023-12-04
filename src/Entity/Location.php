<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_u = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"end date is required")]
    private ?string $start_date = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"start date is required")]
    private ?string $end_date = null;


    #[ORM\Column(nullable: true)]
    private ?int $prix = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Velo $velo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdU(): ?int
    {
        return $this->id_u;
    }

    public function setIdU(int $id_u): static
    {
        $this->id_u = $id_u;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setStartDate(?string $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->end_date;
    }

    public function setEndDate(?string $end_date): static
    {
        $this->end_date = $end_date;

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

    public function getVelo(): ?velo
    {
        return $this->velo;
    }

    public function setVelo(?velo $velo): static
    {
        $this->velo = $velo;

        return $this;
    }
}