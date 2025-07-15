<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, JWTTokenManagerInterface $jwtManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error' => 'Champs manquants'], 400);
        }
        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return $this->json(['error' => 'Identifiants invalides'], 401);
        }
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Identifiants invalides'], 401);
        }
        $token = $jwtManager->create($user);
        return $this->json(['token' => $token]);
    }
} 