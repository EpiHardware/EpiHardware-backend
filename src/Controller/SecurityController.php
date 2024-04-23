<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $JWTManager): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        try {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!$user) {
                return $this->json(['message' => 'User not found.'], Response::HTTP_UNAUTHORIZED);
            }

            if (!$passwordHasher->isPasswordValid($user, $password)) {
                return $this->json(['message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
            }

            $token = $JWTManager->create($user);
            $this->logger->info('User logged in successfully', ['userId' => $user->getId()]);
            return $this->json(['token' => $token], Response::HTTP_OK);

        } catch (\Exception $e) {
            $this->logger->error('Login failed: ' . $e->getMessage());
            return $this->json(['message' => 'Internal server error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function logout(): JsonResponse {

        return $this->json(['message' => 'Successfully logged out.'], Response::HTTP_OK);
    }
}
