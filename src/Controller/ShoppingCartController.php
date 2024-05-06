<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ShoppingCart;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class ShoppingCartController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/api/carts/{productId}', methods: ['POST'])]
    public function addToCart(int $productId, ProductRepository $productRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $product = $productRepository->find($productId);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $cart = $user->getCart();
        if (!$cart) {
            $cart = new ShoppingCart();
            $user->setCart($cart);
            $em->persist($cart);
        }

        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setCart($cart);
        $cart->addItem($cartItem);

        $em->persist($cartItem);
        $em->flush();

        return $this->json(['message' => 'Product added to cart']);
    }

    #[Route('/api/carts/{productId}', methods: ['DELETE'])]
    public function removeFromCart(int $productId, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $cart = $user->getCart();
        $cartItem = $em->getRepository(CartItem::class)->findOneBy(['product' => $productId, 'cart' => $cart]);
        if (!$cartItem) {
            return $this->json(['error' => 'Product not in cart'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($cartItem);
        $em->flush();

        return $this->json(['message' => 'Product removed from cart']);
    }

    #[Route('/api/carts', methods: ['GET'])]
    public function viewCart(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $cart = $user->getCart();
        if (!$cart) {
            return $this->json(['error' => 'Cart is empty'], Response::HTTP_NOT_FOUND);
        }

        $items = $cart->getItems()->map(function (CartItem $item) {
            return [
                'product_id' => $item->getProduct()->getId(),
                'name' => $item->getProduct()->getName(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getProduct()->getPrice()
            ];
        })->toArray();

        return $this->json(['items' => $items]);
    }

    #[Route('/api/carts/validate', methods: ['POST'])]
    public function checkoutCart(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $cart = $user->getCart();
        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->json(['error' => 'Cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($cart->getItems() as $item) {
            $em->remove($item);
        }
        $em->flush();

        return $this->json(['message' => 'Cart has been checked out successfully']);
    }
}
