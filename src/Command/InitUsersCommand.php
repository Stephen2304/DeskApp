<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init-users',
    description: 'Crée un utilisateur admin et deux utilisateurs simples.'
)]
class InitUsersCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersData = [
            [
                'email' => 'admin@example.com',
                'nom' => 'Admin',
                'prenom' => 'Super',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'email' => 'user1@example.com',
                'nom' => 'User',
                'prenom' => 'Un',
                'roles' => ['ROLE_USER'],
            ],
            [
                'email' => 'user2@example.com',
                'nom' => 'User',
                'prenom' => 'Deux',
                'roles' => ['ROLE_USER'],
            ],
        ];
        foreach ($usersData as $data) {
            if ($this->em->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
                $output->writeln("Utilisateur déjà existant : {$data['email']}");
                continue;
            }
            $user = new User();
            $user->setEmail($data['email']);
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $this->em->persist($user);
            $output->writeln("Utilisateur créé : {$data['email']}");
        }
        $this->em->flush();
        $output->writeln('Tous les utilisateurs ont été initialisés.');
        return Command::SUCCESS;
    }
} 