<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ProductController extends AbstractController
{
    #[Route('/api/products', methods: ['POST'])]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPhoto($data['photo']);
        $product->setPrice($data['price']);
        $product->setQuantity($data['quantity']);


        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product added'], Response::HTTP_CREATED);
    }

    #[Route('/api/products', methods: ['GET'])]
    public function getProducts(EntityManagerInterface $entityManager): JsonResponse
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $products = $productRepository->findAll();

        $data = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
            ];
        }, $products);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/products/search', methods: ['GET'])]
    public function searchProducts(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $productName = $request->query->get('name');

        // Vérifiez si $productName est null, si c'est le cas, retournez une réponse appropriée
        if ($productName === null) {
            return new JsonResponse(['message' => 'Product name is missing'], Response::HTTP_BAD_REQUEST);
        }

        $productRepository = $entityManager->getRepository(Product::class);

        // Utilisez une méthode de recherche appropriée dans votre repository, par exemple, findBySearchTerm
        $products = $productRepository->findBySearchTerm($productName);

        $data = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity(),
            ];
        }, $products);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/products/{productId}', methods: ['GET'])]
    public function getProduct(int $productId, EntityManagerInterface $entityManager): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'photo' => $product->getPhoto(),
            'price' => $product->getPrice(),
            'quantity' => $product->getQuantity(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/products/{productId}', methods: ['PUT'])]
    public function updateProduct(Request $request, EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name'] ?? $product->getName());
        $product->setDescription($data['description'] ?? $product->getDescription());
        $product->setPhoto($data['photo'] ?? $product->getPhoto());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setQuantity($data['quantity'] ?? $product->getQuantity());

        $entityManager->flush();

        return new JsonResponse(['status' => 'Product updated'], Response::HTTP_OK);
    }

    #[Route('/api/products/{productId}', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_OK);
    }

}

