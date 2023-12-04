<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\ExcelExportService;
use App\Entity\Station;
use App\Form\Station1Type;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/station')]
class StationController extends AbstractController
{
    
    #[Route('/chicha', name: 'app_station_chicha', methods: ['GET'])]
    public function chicha(StationRepository $stationRepository, ExcelExportService $excelExportService): Response
    {
         // Récupérez les stations depuis le repository
        $stations = $stationRepository->findAll();

        // Exportez les stations vers un fichier Excel
        $excelExportService->exportStationsToExcel($stations);

        // Utilisez le service Symfony\Component\HttpFoundation\BinaryFileResponse pour renvoyer le fichier Excel au client
       
        $response = new BinaryFileResponse('export_stations.xlsx');
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export_stations.xlsx');

        return $response;
        
        
    }



    #[Route('/', name: 'app_station_index', methods: ['GET'])]
    public function index(StationRepository $stationRepository): Response
    {
        return $this->render('station/index.html.twig', [
        'stations' => $stationRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_station_front', methods: ['GET'])]
    public function front(StationRepository $stationRepository ): Response
    {
        return $this->render('station/ff.html.twig', [
        'stations' => $stationRepository->findAll(),
        ]);
    }

  

    #[Route('/new', name: 'app_station_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Station();
        $form = $this->createForm(Station1Type::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('station/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('station/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_station_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Station1Type::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('station/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
    }
}