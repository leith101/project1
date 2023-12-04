<?php

namespace App\Controller;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/stats', name: 'app_statistics')]
    public function statistics(UtilisateurRepository $userRepository): Response
    {
        // Récupérer le nombre total d'utilisateurs depuis le repository
        $totalUsers = $userRepository->getTotalUsers();

        // Vous pouvez utiliser ces données pour générer un schéma ou simplement les afficher
        return $this->render('home/statics.html.twig', [
            'totalUsers' => $totalUsers,
        ]);
    }
}
