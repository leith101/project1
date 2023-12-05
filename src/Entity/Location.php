<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_u = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'start date is required')]
    #[Assert\DateTime(message: "Invalid start date")]
    private ?string $start_date = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'end date is required')]
    #[Assert\DateTime(message: "Invalid end date")]
    private ?string $end_date = null;


    #[ORM\Column(nullable: true)]
    private ?int $prix = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Velo $velo = null;
   // New property to store the price of the associated bicycle
   // Nouvelle méthode pour obtenir le prix du vélo associé
  
    // Nouvelle méthode pour obtenir le prix du vélo associé
    public function getVeloPrice(): ?int
    {
        return $this->velo ? $this->velo->getPrix() : 5;
    }


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

    public function setVelo(?Velo $velo): static
    {
        $this->velo = $velo;

       
        return $this;
    }
   

 /**
     * @return int|null
     */

 /**
     * @Assert\Callback
     */
    public function validateDateRange(ExecutionContextInterface $context, $payload)
    {
        if ($this->start_date && $this->end_date) {
            $startDate = new \DateTime($this->start_date);
            $endDate = new \DateTime($this->end_date);

            // Vérifier si la date de début est inférieure à la date de fin
            if ($startDate > $endDate) {
                $context->buildViolation('La date de début doit être inférieure à la date de fin.')
                    ->atPath('start_date')  // Choisir le champ associé à l'erreur
                    ->addViolation();
            }
        }
    }
} 