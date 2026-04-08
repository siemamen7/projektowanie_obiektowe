<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Order;

#[Route('/orders')]
final class OrderController extends AbstractController
{
    #[Route('', methods: 'GET', name: 'getAll')]
    public function getAll(OrderRepository $repo): JsonResponse
    {
        return $this->json(array_map(function($order) {
            return [
                'id' => $order->getId(),
                'date' => $order->getDate()->format('Y-m-d H:i:s'),
                'total' => $order->getTotal(),
                'product_ids' => array_map(fn($p) => $p->getId(), $order->getProducts()->toArray()),
            ];
        }, $repo->findAll()));
    }

    #[Route('/{id}', methods: 'GET', name: 'getById')]
    public function getById(OrderRepository $repo, int $id): JsonResponse
    {
        $order = $repo->find($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        return $this->json([
            'id' => $order->getId(),
            'date' => $order->getDate()->format('Y-m-d H:i:s'),
            'total' => $order->getTotal(),
            'product_ids' => array_map(fn($p) => $p->getId(), $order->getProducts()->toArray()),
        ]);
    }

    #[Route('', methods: 'POST', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['total'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $order = new Order();
        $order->setDate(new \DateTime());
        $order->setTotal($data['total']);

        $em->persist($order);
        $em->flush();

        return $this->json([
            'id' => $order->getId(),
            'date' => $order->getDate()->format('Y-m-d H:i:s'),
            'total' => $order->getTotal(),
            'product_ids' => array_map(fn($p) => $p->getId(), $order->getProducts()->toArray()),
        ]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, OrderRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $order = $repo->find($id);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['total'])) {
            $order->setTotal($data['total']);
        }

        $em->flush();

        return $this->json([
            'id' => $order->getId(),
            'date' => $order->getDate()->format('Y-m-d H:i:s'),
            'total' => $order->getTotal(),
            'product_ids' => array_map(fn($p) => $p->getId(), $order->getProducts()->toArray()),
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, OrderRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $order = $repo->find($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        $em->remove($order);
        $em->flush();

        return $this->json(['message' => 'Order deleted']);
    }
}