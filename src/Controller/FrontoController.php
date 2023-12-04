<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;

use Endroid\QrCode\Writer\ValidationException;

class FrontoController extends AbstractController
{
    #[Route('/fronto', name: 'app_fronto')]
    public function index(): Response
    {
        return $this->render('fronto/index.html.twig', [
            'controller_name' => 'FrontoController',
        ]);
    }

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/get_event_description', name: 'get_event_description', methods: ['GET'])]
    public function getEventDescription(Request $request): JsonResponse
    {
        $date = $request->query->get('date');
        $place = $request->query->get('place');

        // Fetch event description from the database
        $eventRepository = $this->entityManager->getRepository(Event::class);
        $event = $eventRepository->findOneBy(['date' => $date, 'place' => $place]);

        // Return the event description as JSON
        if ($event) {
            return $this->json(['description' => $event->getDescription()]);
        } else {
            return $this->json(['description' => 'Event not found']);
        }
    }
    #[Route('/load-ticket-content', name: 'load-ticket-content', methods: ['GET'])]
public function loadTicketContent(): Response
{   $writer = new PngWriter();
    
   // $inscription = $inscriptionRepository->find($inscriptionId);
   // $ticketData = $inscription->getTicketData();
    //$qrCode = new QrCode($ticketData);
   $qrCode = new QrCode("tu es le participant nouveau de l'évenement carthage 2023-29-11");

  
    $pngResult = $writer->write($qrCode);

    $qrCodeImage = base64_encode($pngResult->getString());

    
    return $this->render('fronto/qr.html.twig', [
        
        'qrCodeImage' => $qrCodeImage,
    ]);
}
}