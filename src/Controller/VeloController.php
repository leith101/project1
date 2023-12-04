<?php

namespace App\Controller;

use App\Entity\Velo;
use App\Form\VeloType;
use App\Form\SearchType;
use App\Repository\VeloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

use Knp\Component\Pager\PaginatorInterface;
use League\Csv\Writer;




#[Route('/velo')]
class VeloController extends AbstractController
{
    #[Route('/', name: 'app_velo_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, VeloRepository $veloRepository): Response
{
    $query = $veloRepository->findAll();
    $velosPagination = $paginator->paginate($query, $request->query->getInt('page', 1), 4);

    // Calculate the total number of bikes
    $totalVeloCount = $veloRepository->count([]);

    // Calculate the number of available bikes
    $availableVeloCount = $veloRepository->count(['etat' => 'disponible']);

    // Calculate the number of electric bikes
    $electricVeloCount = $veloRepository->count(['modele' => 'electrique']);

    return $this->render('velo/index.html.twig', [
        'velos' => $velosPagination,
        'totalVeloCount' => $totalVeloCount,
        'availableVeloCount' => $availableVeloCount,
        'electricVeloCount' => $electricVeloCount,
    ]);
}
    #[Route('/sortedByPrice', name: 'app_velo_sorted_by_price')]
    public function indexSortedByPrice(VeloRepository $veloRepository): Response
    {
        $velos = $veloRepository->findBy([], ['prix' => 'ASC']); // Tri ascendant par prix

        return $this->render('velo/index.html.twig', [
            'velos' => $velos,
        ]);
    }


    #[Route('/new', name: 'app_velo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $velo = new Velo();
        $form = $this->createForm(VeloType::class, $velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($velo);
            $entityManager->flush();

            // Récupérer les détails du vélo ajouté
            $veloDetails = [
                'id' => $velo->getId(),
                'modele' => $velo->getModele(),
                'etat' => $velo->getEtat(),
                'prix' => $velo->getPrix(),
                // Ajoutez d'autres détails selon votre entité Velo
            ];

            // Envoi du SMS avec les détails du vélo
            //$this->sendSms($veloDetails);
            $this->addFlash('success', 'Le vélo a été créé avec succès !');

            return $this->redirectToRoute('app_velo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('velo/new.html.twig', [
            'velo' => $velo,
            'form' => $form,
        ]);
    }
    #[Route('/velo/export', name: 'app_velo_export')]
    public function export(VeloRepository $veloRepository): StreamedResponse
    {
        // Récupérez vos données de vélo depuis le référentiel (repository)
        $velos = $veloRepository->findAll();

        // Créez une réponse StreamedResponse
        $response = new StreamedResponse();

        $response->setCallback(function () use ($velos) {
            // Configurez l'en-tête de la réponse
            $outputStream = fopen('php://output', 'w');

            // Ajoutez l'en-tête du fichier CSV
            fputcsv($outputStream, ['Id', 'Modele', 'Etat', 'Prix', 'Image', 'Station']);

            // Ajoutez chaque ligne de données
            foreach ($velos as $velo) {
                fputcsv($outputStream, [$velo->getId(), $velo->getModele(), $velo->getEtat(), $velo->getPrix(), $velo->getImage(), $velo->getStation()]);
            }

            fclose($outputStream);
        });

        // Configurez l'en-tête de la réponse
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="velos.csv"');

        return $response;
    }
  /*  private function sendSms(array $veloDetails): void
    {
        $accountSid = $this->getParameter('AC1dbfffb804d9fc83ed912647478edffb');
        $authToken = $this->getParameter('8b85160e1a5ef9252b480889a9968519');
        $twilioPhoneNumber = $this->getParameter('+21620264013');

        $client = new Client($accountSid, $authToken);

        $messageBody = sprintf(
            'Nouveau vélo ajouté - ID: %s, Modèle: %s, État: %s, Prix: %s',
            $veloDetails['id'],
            $veloDetails['modele'],
            $veloDetails['etat'],
            $veloDetails['prix']
            // Ajoutez d'autres détails selon votre entité Velo
        );

        $message = $client->messages->create(
            '+21658699003', // Remplacez par le numéro de téléphone de destination
            [
                'from' => $twilioPhoneNumber,
                'body' => $messageBody,
            ]
        );

        // Gérer la réponse du service SMS
        dump($message);
    }
*/

    #[Route('/{id}', name: 'app_velo_show', methods: ['GET'], requirements: ['id' => '\d+'])]    
    public function show(int $id, VeloRepository $veloRepository): Response
    {
        $velo = $veloRepository->findVeloById($id);
        return $this->render('velo/show.html.twig', [
            'velo' => $velo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_velo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Velo $velo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VeloType::class, $velo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le vélo a été modifié avec succès !');


            return $this->redirectToRoute('app_velo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('velo/edit.html.twig', [
            'velo' => $velo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_velo_delete', methods: ['POST'])]
    public function delete(Request $request, Velo $velo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$velo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($velo);
            $entityManager->flush();
            $this->addFlash('success', 'Le vélo a été supprimé avec succès !');

        }

        return $this->redirectToRoute('app_velo_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/search', name: 'app_velo_search', methods: ['GET', 'POST'])]
    public function search(Request $request, VeloRepository $veloRepository): Response
    {
        $velos = $veloRepository->findAll(); // Ou utilisez une méthode spécifique de recherche si nécessaire
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
    
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $criteria = $searchForm->getData();
    
            // Utiliser les critères pour effectuer la recherche
            $resultOfSearch = $veloRepository->findBy(['modele' => $criteria['modele']]);
    
            return $this->render('velo/search.html.twig', [
                'velos' => $resultOfSearch,
                'search' => $searchForm,
            ]);
        }
    
        return $this->render('velo/search.html.twig', [
            'velos' => $velos,
            'search' => $searchForm,
        ]);
    }}
    
    