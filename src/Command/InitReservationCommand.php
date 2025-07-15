<?php
namespace App\Command;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Desk;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:init-reservations',
)]
class InitReservationCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->em->getRepository(User::class)->find(1);
        if (!$user) {
            $output->writeln('<error>Utilisateur admin non trouvé.</error>');
            return Command::FAILURE;
        }
        $desks = $this->em->getRepository(Desk::class)->findAll();
        if (count($desks) === 0) {
            $output->writeln('<error>Aucun bureau trouvé.</error>');
            return Command::FAILURE;
        }
        $reservationsData = [
            [
                'desk' => $desks[0],
                'date_debut' => new \DateTime('+1 day'),
                'date_fin' => new \DateTime('+2 days'),
                'statut' => 'confirmée',
            ],
            [
                'desk' => $desks[1 % count($desks)],
                'date_debut' => new \DateTime('+3 days'),
                'date_fin' => new \DateTime('+4 days'),
                'statut' => 'en attente',
            ],
            [
                'desk' => $desks[2 % count($desks)],
                'date_debut' => new \DateTime('+5 days'),
                'date_fin' => new \DateTime('+6 days'),
                'statut' => 'annulée',
            ],
        ];
        foreach ($reservationsData as $data) {
            $reservation = new Reservation();
            $reservation->setUtilisateur($user);
            $reservation->setBureau($data['desk']);
            $reservation->setDateDebut($data['date_debut']);
            $reservation->setDateFin($data['date_fin']);
            $reservation->setStatut($data['statut']);
            $this->em->persist($reservation);
        }
        $this->em->flush();
        $output->writeln('<info>3 réservations créées pour l’utilisateur admin.</info>');
        return Command::SUCCESS;
    }
} 