<?php

namespace App\Entity;

use App\Repository\HediRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HediRepository::class)]
class Hedi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
