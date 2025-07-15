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

use OpenApi\Annotations as OA;

#[Route('/api/desks')]
class DeskController extends AbstractController
{
    /**
     * Liste tous les bureaux
     *
     * @OA\Get(
     *     path="/api/desks/",
     *     summary="Liste tous les bureaux",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des bureaux"
     *     )
     * )
     */
    #[Route('/', name: 'desk_index', methods: ['GET'])]
    public function index(DeskRepository $deskRepository): Response
    {
        return $this->json($deskRepository->findAll());
    }

    /**
     * Réserver un bureau
     *
     * @OA\Post(
     *     path="/api/desks/{id}/reserver",
     *     summary="Réserver un bureau",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du bureau à réserver",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="date_debut", type="string", example="2024-06-01T09:00:00"),
     *                 @OA\Property(property="date_fin", type="string", example="2024-06-01T18:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Réservation créée"
     *     )
     * )
     */
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

    /**
     * Liste les réservations de l'utilisateur connecté
     *
     * @OA\Get(
     *     path="/api/desks/me/reservations",
     *     summary="Liste les réservations de l'utilisateur connecté",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des réservations"
     *     )
     * )
     */
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