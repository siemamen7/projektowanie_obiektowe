<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Category;

#[Route('/categories')]
final class CategoryController extends AbstractController
{
    #[Route('', methods: 'GET', name: 'getAll')]
    public function getAll(CategoryRepository $repo): JsonResponse
    {
        return $this->json(array_map(function($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'product_count' => $category->getProducts()->count(),
            ];
        }, $repo->findAll()));
    }

    #[Route('/{id}', methods: 'GET', name: 'getById')]
    public function getById(CategoryRepository $repo, string $id): JsonResponse
    {
        $category = $repo->find((int) $id);
        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }
        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'product_count' => $category->getProducts()->count(),
        ]);
    }

    #[Route('', methods: 'POST', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $category = new Category();
        $category->setName($data['name']);

        $em->persist($category);
        $em->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'product_count' => $category->getProducts()->count(),
        ]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(string $id, Request $request, CategoryRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $category = $repo->find((int) $id);

        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->setName($data['name']);
        }

        $em->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'product_count' => $category->getProducts()->count(),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id, CategoryRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $category = $repo->find((int) $id);
        if (!$category) {
            return $this->json(['error' => 'Category not found'], 404);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Category deleted']);
    }

    #[Route('/view', name: 'category_view')]
    public function view(CategoryRepository $repo)
    {
        return $this->render('category/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }
}