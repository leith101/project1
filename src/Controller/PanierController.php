<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitRepository;
#[Route('/panier')]
class PanierController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }






    ////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////
    #[Route('/panier/add/{idprod}', name: 'app_panier_add')]
    public function addToCart($idprod, ProduitRepository $repo)
    {

        $product = $repo->find($idprod);
        $cart = $this->session->get('cart', []);

        if (!isset($cart[$idprod])) {
            $cart[$idprod] = [
                'id' => $idprod,
                'name' => $product->getNomprod(),
                'price' => $product->getPrixprod(),
                'image' => $product->getImageprod(),
                'description' => $product->getDescriptionprod(),
                'quantity' => 0,
                'total' => 0
            ];
        }
        if (isset($_POST["quantite"])) {
            $quantite = intval($_POST["quantite"]);
            if ($quantite == 0)
                $quantite = 1;
        } else
            $quantite = 1;

        $cart[$idprod]['quantity'] += $quantite;
        $cart[$idprod]['total'] = $quantite * $product->getPrixprod();

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('app_panier_index');
    }
    ////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////

    #[Route('/', name: 'app_panier_index')]
    public function showCart()
    {
        $panier = $this->session->get('cart', []);
        $numberOfItems = count($panier);
        // Calculer le montant total du panier
    $totalAmount = 0;
    foreach ($panier as $item) {
        $totalAmount += $item['total'] * $item['quantity'];
    }

        return $this->render('panier/panier.html.twig', [
            'paniers' => $panier,
            'numberOfItems' => $numberOfItems,
            'totalAmount' => $totalAmount, // Envoyer le montant total à la vue
        ]);
    }

    #[Route('/delete/{id}', name: 'app_panier_delete')]
    public function removeFromCart($id)
    {
        $cart = $this->session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('app_panier_index');
    }
    #[Route('/clear-cart', name: 'clear_cart')]
    public function clearCart(SessionInterface $session): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        // Clear the cart session
        $session->remove('cart');

        // Redirect to the app_produit_front route
        
        return $this->redirectToRoute('app_produit_front');
    }
}
