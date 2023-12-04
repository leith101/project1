<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
             'produits' => $produits,
        ]);
    }

    #[Route('/front', name: 'app_produit_front', methods: ['GET'])]
    public function produitfront(EntityManagerInterface $entityManager, Request $request): Response
    {


        $query = $entityManager->getRepository(Produit::class)
        ->createQueryBuilder('p')
        ->orderBy('p.idprod', 'DESC')
        ->getQuery();


        $produits = new Paginator($query);

        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 3;

        $produits
        ->getQuery()
        ->setFirstResult($itemsPerPage * ($currentPage - 1))
        ->setMaxResults($itemsPerPage);

        $totalItems = count($produits);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        return $this->render('produit/show.html.twig', [
            'produits' => $produits,
            'CurrentPage' => $currentPage,
            'pagesCount' => $pagesCount,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageprod')->getData();
            if ($file) {
                try {
                    $filename = uniqid() . '.' . $file->guessExtension();
                    $file->move('produitimages', $filename);
                    $produit->setImageprod($filename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_produit_new');
                }
            }
    
            try {
                $entityManager->persist($produit);
                $entityManager->flush();
    
                $this->addFlash('success', 'Le produit a été créé avec succès.');
                return $this->redirectToRoute('app_produit_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du produit.');
                var_dump($e->getMessage()); // Output the error message for debugging
            }
        } 
        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }
    

    #[Route('/{idprod}', name: 'app_produit_single', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/single.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{idprod}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() /*&& $form->isValid()*/) {
            if ($form->get('imageprod')->getData()) {
                $file = $form->get('imageprod')->getData();

                // If a file was uploaded
                if ($file) {
                    $filename = uniqid() . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move(
                        'produitimages',
                        $filename
                    );
                    
                    // Update the 'image' property to store the image file name
                    // instead of its contents
                    $produit->setImageprod($filename);
                   
                }
            } else {
                // Keep the old profile picture
                $produit->setImageprod($produit->getImageprod());
            }
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{idprod}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
