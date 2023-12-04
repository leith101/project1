<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
   
      #[ORM\Column(name:"idPanier", type:"integer", nullable:false)]
      #[ORM\Id]
      #[ORM\GeneratedValue(strategy:"IDENTITY")]

    private ?int $idpanier;


   #[ORM\Column(name:"etat", type:"integer", nullable:false)]
    
    private ?int $etat;

     #[ORM\Column(name:"userId", type:"integer", nullable:false)]
     
    private ?int $userid;

   
     #[ORM\Column(name:"prix", type:"integer", nullable:true)]
     
    private ?int $prix = 0;

    
    /**
 * @ORM\ManyToMany(targetEntity="Produit", inversedBy="panier")
 * @ORM\JoinTable(name="panier_produit",
 *      joinColumns={
 *          @ORM\JoinColumn(name="panier_id", referencedColumnName="id")
 *      },
 *      inverseJoinColumns={
 *          @ORM\JoinColumn(name="produit_id", referencedColumnName="id")
 *      }
 * )
 */
    private $produit = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->produit = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdpanier(): ?int
    {
        return $this->idpanier;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        $this->produit->removeElement($produit);

        return $this;
    }

}
