<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(Request $request, ReclamationRepository $reclamationRepository, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        
        $userId = $session->get('userId'); // Remplacez 1 par l'ID de l'utilisateur ajouté statiquement dans le formulaire
        $reclamations = $reclamationRepository->findByUserIdFilteredAndSorted($userId);
        $filterBy = $request->query->get('filter_by');
        $sortBy = $request->query->get('sort_by', 'date_reclamation'); // Tri par défaut sur la date

        // Utilisation des paramètres pour construire la requête en filtrant par l'ID de l'utilisateur statique
        $query = $reclamationRepository->getFilteredAndSortedQuery($userId, $filterBy, $sortBy);

        $reclamations = $reclamationRepository->findByUserIdFilteredAndSorted($userId, $filterBy, $sortBy);

$pagination = $paginator->paginate(
    $reclamations,
    $request->query->getInt('page', 1),
    10
);
$userReclamations = $reclamationRepository->findByUserIdFilteredAndSorted($userId);

// Récupérer les statistiques pour l'état des réclamations de cet utilisateur
$etatReclamationStats = $reclamationRepository->getStatsForUserByEtat($userId);
    
        return $this->render('reclamation/index.html.twig', [
            'pagination' => $pagination,
            'etat_stats' => $etatReclamationStats,
           
        ]);
    }
    #[Route('/stats', name: 'app_stats', methods: ['GET'])]
public function showStats(ReclamationRepository $reclamationRepository): Response
{
    // Récupération des statistiques depuis le repository
    $stats = $reclamationRepository->getStats();

    // Récupération des données pour le graphique
    $labels = [];
    $data = [];

    foreach ($stats as $stat) {
        $labels[] = $stat['categorie_reclamation'];
        $data[] = $stat['nombreReclamations'];
    }

    // Retourne la vue Twig des statistiques avec les données
    return $this->render('reclamation/stat.html.twig', [
        'stats' => $stats,
        'labels' => json_encode($labels),
        'data' => json_encode($data),
    ]);
}

 



    
    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        
        $reclamation = new Reclamation();
        $reclamation->setEtatReclamation(false); 
        $reclamation->setDateReclamation(new \DateTime()); // Ajoutez la date actuelle
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
        
        // Liste de mots interdits
        $badWords = ['mot1', 'mot2', 'mot3']; // Remplacez par votre liste de mots interdits
        $descriptionReclamation = $reclamation->getDescriptionReclamation();
        
        foreach ($badWords as $badWord) {
            if (stripos($descriptionReclamation, $badWord) !== false) {
                // Affichage d'une alerte JavaScript pour les mots interdits
                $this->addFlash('danger', 'La description contient des mots interdits.');
                return $this->redirectToRoute('app_reclamation_new');
            }
        }
        if (strlen($reclamation->getDescriptionReclamation()) > 15) {
            $this->addFlash('danger', 'La description ne peut pas dépasser 15 caractères.');
            return $this->redirectToRoute('app_reclamation_new');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $form->get('idUtilisateur')->getData();
            $entityManager->persist($reclamation);
            $session->set('userId', $userId);
            $session->getFlashBag()->add('success', 'Votre réclamation a été créée avec succès.');
            
            $entityManager->flush();
            
            
        }
    
        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id_reclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
         $idUtilisateurReclamation = $reclamation->getIdUtilisateur();
         $user = $this->getUser();
         if ($user && $user->getId() === $idUtilisateurReclamation) {
            return $this->render('reclamation/show.html.twig', [
                'reclamation' => $reclamation,
            ]);
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    

    #[Route('/{id_reclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            $this->addFlash('success', 'La réclamation a été modifiée avec succès.');

            // Ajout de la clé 'success_message_displayed' dans la session pour indiquer que le message de succès doit être affiché
            $session->set('success_message_displayed', true);
    
            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id_reclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId_reclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}