<?php

namespace App\Controller;

use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques')]
    public function statistiques(
        ChartBuilderInterface $chartBuilder,
        ReclamationRepository $reclamationRepository,
        ReponseRepository $reponseRepository
    ): Response {
        // Obtenez les données pour les statistiques
        $statistiquesReclamation = $reclamationRepository->getStats(); // Méthode fictive pour récupérer les données des réclamations
        
        
        // Créez un graphique basé sur les données récupérées
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => array_keys($statistiquesReclamation),
            'datasets' => [
                [
                    'label' => 'Réclamations',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => array_values($statistiquesReclamation),
                ],
                
            ],
        ]);

        return $this->render('statistiques/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}