<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')] // toutes les pages de ce contrôleur sont réservées aux admins
final class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['validated' => false], ['date' => 'ASC']);

        return $this->render('validate/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/validate/{id}', name: 'app_validate_event', methods: ['POST'])]
    public function validate(Event $event, EntityManagerInterface $entityManager): Response
    {
        $event->setValidated(true);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement « '.$event->getTitle().' » a été validé.');

        return $this->redirectToRoute('app_validate');
    }
    
    #[Route('/validate/{id}/refuser', name: 'app_reject_event', methods: ['POST'])]
    public function reject(Event $event, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'L\'événement « '.$event->getTitle().' » a été refusé.');

        return $this->redirectToRoute('app_validate');
    }
}
