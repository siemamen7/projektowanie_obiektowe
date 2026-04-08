<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Repository\CategoryRepository;

#[Route('/products')]
final class ProductController extends AbstractController
{
    #[Route('', methods: 'GET', name: 'getAll')]
    public function getAll(ProductRepository $repo): JsonResponse
    {
        return $this->json(array_map(function($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'category_id' => $product->getCategory()?->getId(),
            ];
        }, $repo->findAll()));
    }

    #[Route('/{id}', methods: 'GET', name: 'getById', requirements: ['id' => '\d+'])]
    public function getById(ProductRepository $repo, string $id): JsonResponse
    {
        $product = $repo->find((int) $id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'category_id' => $product->getCategory()?->getId(),
        ]);
    }

    #[Route('', methods: 'POST', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name']) || !isset($data['price'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        if (isset($data['category_id'])) {
            $category = $categoryRepo->find($data['category_id']);
            if (!$category) {
                return $this->json(['error' => 'Category not found'], 404);
            }
            $product->setCategory($category);
        }

        $em->persist($product);
        $em->flush();

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'category_id' => $product->getCategory()?->getId(),
        ]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(string $id, Request $request, ProductRepository $repo, EntityManagerInterface $em, CategoryRepository $categoryRepo): JsonResponse
    {
        $product = $repo->find((int) $id);

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

        if (isset($data['category_id'])) {
            $category = $categoryRepo->find($data['category_id']);
            if (!$category) {
                return $this->json(['error' => 'Category not found'], 404);
            }
            $product->setCategory($category);
        }

        $em->flush();

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'category_id' => $product->getCategory()?->getId(),
        ]);
    }
    
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id, ProductRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $product = $repo->find((int) $id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted']);
    }

    #[Route('/view', name: 'product_view')]
    public function view(ProductRepository $repo)
    {
        return $this->render('product/index.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }
}
