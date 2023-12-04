<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
   
    private int $id_reponse;

    #[ORM\Column(length: 255)]
    private ?string $description_reponse = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $date_reponse = null; // nouvel attribut dateReponse

    public function getIdReponse(): ?int
    {
        return $this->id_reponse;
    }

    public function setIdReponse(int $id_reponse): self
    {
        $this->id_reponse = $id_reponse;

        return $this;
    } 

    public function getDescriptionReponse(): ?string
    {
        return $this->description_reponse;
    }

    public function setDescriptionReponse(string $description_reponse): self
    {
        $this->description_reponse = $description_reponse;

        return $this;
    }
    

	/**
	 * @return string|null
	 */
	public function getDescription_reponse(): ?string {
		return $this->description_reponse;
	}
	
	/**
	 * @param string|null $description_reponse 
	 * @return self
	 */
	public function setDescription_reponse(?string $description_reponse): self {
		$this->description_reponse = $description_reponse;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId_reponse(): int {
		return $this->id_reponse;
	}

     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reclamation", inversedBy="reponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reclamation;

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;

        return $this;
    }

	/**
	 * @return DateTimeInterface|null
	 */
	public function getDate_reponse(): ?DateTimeInterface {
		return $this->date_reponse;
	}
	
	/**
	 * @param DateTimeInterface|null $date_reponse 
	 * @return self
	 */
	public function setDate_reponse(?DateTimeInterface $date_reponse): self {
		$this->date_reponse = $date_reponse;
		return $this;
	}
    public function getDateReponse(): ?DateTimeInterface {
		return $this->date_reponse;
	}
    public function setDateReponse(?DateTimeInterface $date_reponse): self {
		$this->date_reponse = $date_reponse;
		return $this;
	}
}