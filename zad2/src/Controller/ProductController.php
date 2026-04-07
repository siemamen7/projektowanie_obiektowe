<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;

#[Route('/products')]
final class ProductController extends AbstractController
{
    #[Route('', methods: 'GET', name: 'getAll')]
    public function getAll(ProductRepository $repo): JsonResponse
    {
        return $this->json($repo->findAll());
    }

    #[Route('/{id}', methods: 'GET', name: 'getById')]
    public function getById(ProductRepository $repo, int $id): JsonResponse
    {
        $product = $repo->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        return $this->json($product);
    }

    #[Route('', methods: 'POST', name: 'create')]
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name']) || !isset($data['price'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        $em->persist($product);
        $em->flush();

        return $this->json($product, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, ProductRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $product = $repo->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }

        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }

        $em->flush();

        return $this->json($product);
    }
    
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProductRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $product = $repo->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted']);
    }
}
