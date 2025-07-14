<?php
namespace App\Controller;

use App\Entity\Desk;
use App\Enum\BureauType;
use App\Entity\Reservation;
use App\Repository\DeskRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/desks')]
class DeskController extends AbstractController
{
    #[Route('/', name: 'desk_index', methods: ['GET'])]
    public function index(DeskRepository $deskRepository): Response
    {
        return $this->json($deskRepository->findAll());
    }

    #[Route('/create', name: 'desk_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $desk = new Desk();
        $desk->setNom($data['nom'] ?? '');
        $desk->setLocalisation($data['localisation'] ?? '');
        $desk->setCapacite($data['capacite'] ?? 1);
        $desk->setType(BureauType::from($data['type'] ?? 'autre'));
        $em->persist($desk);
        $em->flush();
        return $this->json($desk, 201);
    }

    #[Route('/{id}/show', name: 'desk_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Desk $desk): Response
    {
        return $this->json($desk);
    }

    #[Route('/{id}', name: 'desk_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Desk $desk, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['nom'])) $desk->setNom($data['nom']);
        if (isset($data['localisation'])) $desk->setLocalisation($data['localisation']);
        if (isset($data['capacite'])) $desk->setCapacite($data['capacite']);
        if (isset($data['type'])) $desk->setType(BureauType::from($data['type']));
        $em->flush();
        return $this->json($desk);
    }

    #[Route('/{id}/delete', name: 'desk_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Desk $desk, EntityManagerInterface $em): Response
    {
        $em->remove($desk);
        $em->flush();
        return $this->json(null, 204);
    }

    #[Route('/{id}/reserver', name: 'desk_reserver', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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
        return $this->json($reservation, 201);
    }

    #[Route('/reservations', name: 'reservation_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function reservations(Request $request, ReservationRepository $reservationRepository): Response
    {
        $nom = $request->query->get('nom');
        $type = $request->query->get('type');
        $qb = $reservationRepository->createQueryBuilder('r')
            ->join('r.bureau', 'd')
            ->where('r.utilisateur = :user')
            ->setParameter('user', $this->getUser());
        if ($nom) {
            $qb->andWhere('d.nom LIKE :nom')->setParameter('nom', "%$nom%");
        }
        if ($type) {
            $qb->andWhere('d.type = :type')->setParameter('type', $type);
        }
        $reservations = $qb->getQuery()->getResult();
        return $this->json($reservations);
    }

    #[Route('/users/{id}/reservations', name: 'user_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function userReservations(int $id, ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->createQueryBuilder('r')
            ->join('r.utilisateur', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
        return $this->json($reservations);
    }

    #[Route('/me/reservations', name: 'my_reservations', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myReservations(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $reservations = $reservationRepository->createQueryBuilder('r')
            ->where('r.utilisateur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
        return $this->json($reservations);
    }
} 