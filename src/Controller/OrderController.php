<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    private Security $security;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    // Fetch all orders for the authenticated user with details
    #[Route('/', name: 'get_orders', methods: ['GET'])]
    public function getOrders(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->json(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        }

        $orders = $this->entityManager->getRepository(Order::class)->findBy(['user' => $user]);
        $data = $this->serializer->serialize($orders, 'json', [
            'groups' => ['order:read', 'order:items'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // Fetch a specific order by ID with details
    #[Route('/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->json(['message' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        }

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id, 'user' => $user]);
        if (!$order) {
            return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($order, 'json', [
            'groups' => ['order:read', 'order:items'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
