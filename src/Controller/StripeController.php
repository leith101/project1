<?php

namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe ;
class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(Request $request): Response
    {
        $totalAmount = $request->request->get('total_amount'); // Récupérer le montant total depuis la requête POST

        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'totalAmount' => $totalAmount, // Envoyer le montant total à la vue
        ]);
    }
 
 
    /**
     * @Route("/stripe/create-charge", name="stripe_charge", methods={"POST"})
     */
    public function createCharge(Request $request)
{
    $totalAmount = $request->request->get('total_amount'); // Récupérer le montant total depuis la requête POST

    Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
    Stripe\Charge::create([
        "amount" => $totalAmount * 100, // Convertir le montant en centimes si nécessaire
        "currency" => "usd", // Modifier la devise si nécessaire
        "source" => $request->request->get('stripeToken'),
        "description" => "Payment Test"
    ]);
    
    $this->addFlash(
        'success',
        'Payment Successful!'
    );
    return $this->redirectToRoute('app_stripe');
}
}