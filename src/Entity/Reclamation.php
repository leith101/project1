<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Renderer\Image\Png;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
   
    private ?int $id_reclamation;
    #[ORM\Column]
    private ?int $id_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie_reclamation = null;

    #[ORM\Column(length: 255)]
    private ?string $objet_reclamation = null;

    #[ORM\Column(length: 255)]
    private ?string $description_reclamation = null;

    #[ORM\Column]
    private ?int $etat_reclamation = null;

   
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $date_reclamation = null; 

    

    public function getIdReclamation(): ?int
    {
        return $this->id_reclamation;
    }

    public function setIdReclamation(int $id_reclamation): self
    {
        $this->id_reclamation = $id_reclamation;

        return $this;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(int $id_utilisateur): self
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getCategorieReclamation(): ?string
    {
        return $this->categorie_reclamation;
    }

    public function setCategorieReclamation(string $categorie_reclamation): self
    {
        $this->categorie_reclamation = $categorie_reclamation;

        return $this;
    }

    public function getObjetReclamation(): ?string
    {
        return $this->objet_reclamation;
    }

    public function setObjetReclamation(string $objet_reclamation): self
    {
        $this->objet_reclamation = $objet_reclamation;

        return $this;
    }

    public function getDescriptionReclamation(): ?string
    {
        return $this->description_reclamation;
    }

    public function setDescriptionReclamation(string $description_reclamation): self
    {
        $this->description_reclamation = $description_reclamation;

        return $this;
    }

    public function getEtatReclamation(): ?int
    {
        return $this->etat_reclamation;
    }

    public function setEtatReclamation(int $etat_reclamation): self
    {
        $this->etat_reclamation = $etat_reclamation;

        return $this;
    }

     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reponse", mappedBy="reclamation")
     */
    private $reponses;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setReclamation($this);
        }

        return $this;
    }
    public function getId(): ?int
{
    return $this->id_reclamation;
}


    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reponse[]
     */
    public function getReponses(): Collection
    {
     return $this->reponses ?: new ArrayCollection();
        
    }

    

	/**
	 * @return DateTimeInterface|null
	 */
	public function getDate_reclamation(): ?DateTimeInterface {
         		return $this->date_reclamation;
         	}
	
	/**
	 * @param DateTimeInterface|null $date_reclamation 
	 * @return self
	 */
	public function setDate_reclamation(?DateTimeInterface $date_reclamation): self {
         		$this->date_reclamation = $date_reclamation;
         		return $this;
         	}
    /**
 * @return DateTimeInterface|null
 */
public function getDateReclamation(): ?DateTimeInterface
{
    return $this->date_reclamation;
}

/**
 * @param DateTimeInterface|null $date_reclamation
 * @return self
 */
public function setDateReclamation(?DateTimeInterface $date_reclamation): self
{
    $this->date_reclamation = $date_reclamation;
    return $this;
}


private $signale;

  public function setSignale($value) {
    $this->signale = $value;
  }






	/**
	 * @return mixed
	 */
	public function getSignale() {
		return $this->signale;
	}
    
   
}