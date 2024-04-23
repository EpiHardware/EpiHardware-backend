<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'api_orders', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EntityManagerInterface $entityManager): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_FORBIDDEN);
        }

        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        return $this->json($orders);
    }

    #[Route('/{orderId}', name: 'api_order_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $orderId, EntityManagerInterface $entityManager): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_FORBIDDEN);
        }

        $order = $entityManager->getRepository(Order::class)->find($orderId);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user !== $order->getUser()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        return $this->json($order);
    }
}
