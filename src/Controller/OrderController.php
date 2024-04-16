<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    #[Route('/api/orders', methods: ['GET'])]
    public function getAllOrders(OrderRepository $orderRepository, UserInterface $user): JsonResponse {
        $orders = $orderRepository->findBy(['user' => $user->getId()]);
        return $this->json($orders);
    }

    #[Route('/api/orders/{orderId}', methods: ['GET'])]
    public function getOrder(int $orderId, OrderRepository $orderRepository, UserInterface $user): JsonResponse {
        $order = $orderRepository->findOneBy(['id' => $orderId, 'user' => $user->getId()]);
        if (!$order) {
            return $this->json(['message' => 'Order not found'], 404);
        }
        return $this->json($order);
    }
}

