<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ProductController extends AbstractController
{


    #[Route('/api/products', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        $data = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'photo' => $product->getPhoto()
            ];
        }, $products);

        return new JsonResponse($data);
    }

    #[Route('/api/products/{productId}', methods: ['GET'])]
    public function productDetail(ProductRepository $productRepository, int $productId): JsonResponse
    {
        $product = $productRepository->find($productId);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'photo' => $product->getPhoto()
        ];

        return new JsonResponse($data);
    }


    #[Route('/api/products', methods: ['POST'])]
    public function addProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setPhoto($data['photo']);

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product created', 'id' => $product->getId()], Response::HTTP_CREATED);
    }


    #[Route('/api/products/{productId}', methods: ['PUT', 'DELETE'])]
    public function modifyDeleteProduct(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager, int $productId): JsonResponse
    {
        $product = $productRepository->find($productId);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        if ($request->getMethod() === 'PUT') {
            $data = json_decode($request->getContent(), true);
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setPhoto($data['photo']);

            $entityManager->flush();

            return new JsonResponse(['status' => 'Product updated'], Response::HTTP_OK);
        } elseif ($request->getMethod() === 'DELETE') {
            $entityManager->remove($product);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['error' => 'Method not supported'], Response::HTTP_BAD_REQUEST);
    }



}
