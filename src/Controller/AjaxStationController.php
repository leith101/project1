<?php



// src/Controller/AjaxStationController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Station;

#[Route('/ajax/station')]
class AjaxStationController extends AbstractController
{
    #[Route('/like/{id}', name: 'ajax_station_like', methods: ['POST'])]
    public function like(Station $station): JsonResponse
    {
        $station->setLikes($station->getLikes() + 1);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['likes' => $station->getLikes()]);
    }

    #[Route('/dislike/{id}', name: 'ajax_station_dislike', methods: ['POST'])]
    public function dislike(Station $station): JsonResponse
    {
        $station->setDislikes($station->getDislikes() + 1);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['dislikes' => $station->getDislikes()]);
    }
}