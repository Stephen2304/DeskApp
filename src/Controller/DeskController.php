<?php
namespace App\Controller;

use App\Entity\Desk;
use App\Entity\Reservation;
use App\Repository\DeskRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/desks')]
class DeskController extends AbstractController
{
    #[Route('/', name: 'desk_index', methods: ['GET'])]
    public function index(DeskRepository $deskRepository): Response
    {
        return $this->json($deskRepository->findAll());
    }

    #[Route('/{id}/reserver', name: 'desk_reserver', methods: ['POST'])]
    public function reserver(Desk $desk, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $reservation = new Reservation();
        $reservation->setUtilisateur($this->getUser());
        $reservation->setBureau($desk);
        $reservation->setDateDebut(new \DateTime($data['date_debut']));
        $reservation->setDateFin(new \DateTime($data['date_fin']));
        $reservation->setStatut('en attente');
        $em->persist($reservation);
        $em->flush();
        return $this->json($reservation, 201, [], ['groups' => 'reservation:read']);
    }

    #[Route('/me/reservations', name: 'my_reservations', methods: ['GET'])]
    public function myReservations(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $reservations = $reservationRepository->createQueryBuilder('r')
            ->where('r.utilisateur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        return $this->json($reservations, 200, [], ['groups' => 'reservation:read']);
    }
} 