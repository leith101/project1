<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\Utilisateur2Type;
use App\Form\SearchFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


#[Route('/utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

     /**
     * @Route("/export-users-excel", name="export_users_excel")
     */
    public function exportToExcel(UtilisateurRepository $utilisateurRepository): Response
{
    // Récupérez la liste des utilisateurs depuis la base de données
    $utilisateurs = $utilisateurRepository->findAll();

    // Créez une instance de la classe Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Ajoutez les données à la feuille
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'ID Utilisateur');
    $sheet->setCellValue('B1', 'Nom d\'utilisateur');
    $sheet->setCellValue('C1', 'Email');

    // Remplissez les données des utilisateurs
    $row = 2;
    foreach ($utilisateurs as $utilisateur) {
        $sheet->setCellValue('A' . $row, $utilisateur->getId());
        $sheet->setCellValue('B' . $row, $utilisateur->getUsernamee());
        $sheet->setCellValue('C' . $row, $utilisateur->getEmail());
        $row++;
    }

    // Générez le fichier Excel
    $writer = new Xlsx($spreadsheet);

    // Capturez la sortie dans une variable
    ob_start();
    $writer->save('php://output');
    $excelContent = ob_get_clean();

    // Créez une réponse pour le fichier Excel
    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment;filename="export_utilisateurs.xlsx"');
    $response->headers->set('Cache-Control', 'max-age=0');

    // Affectez la sortie à la réponse
    $response->setContent($excelContent);

    // Envoyez la réponse
    return $response;
}

    #[Route('/search', name: 'search')]
public function search(Request $request, UtilisateurRepository $utilisateur)
{
    $utilisateurs = $utilisateur->findAll();
    $searchForm = $this->createForm(SearchFormType::class);
    $searchForm->handleRequest($request);

    if ($searchForm->isSubmitted()) {
        $searchTerm = $searchForm['username']->getData();
        $resultOfSearch = $utilisateur->findByUsername($searchTerm);

        return $this->renderForm('utilisateur/search.html.twig', [
            'utilisateurs' => $resultOfSearch,
            'search' => $searchForm,
        ]);
    }

    return $this->renderForm('utilisateur/search.html.twig', [
        'utilisateurs' => $utilisateurs,
        'search' => $searchForm,
    ]);
}


    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(Utilisateur2Type::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{idUser}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Utilisateur2Type::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
