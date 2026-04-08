<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Order;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    // Products
    #[Route('/products', name: 'admin_products')]
    public function products(ProductRepository $repo): Response
    {
        return $this->render('admin/products/list.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }

    #[Route('/products/new', name: 'admin_product_new', methods: ['GET', 'POST'])]
    public function newProduct(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo): Response
    {
        if ($request->isMethod('POST')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setPrice((float)$request->request->get('price'));
            
            $categoryId = $request->request->get('category_id');
            if ($categoryId) {
                $category = $categoryRepo->find($categoryId);
                if ($category) {
                    $product->setCategory($category);
                }
            }
            
            $em->persist($product);
            $em->flush();
            
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/form.html.twig', [
            'categories' => $categoryRepo->findAll(),
            'product' => null,
        ]);
    }

    #[Route('/products/{id}/edit', name: 'admin_product_edit', methods: ['GET', 'POST'])]
    public function editProduct(int $id, Request $request, ProductRepository $repo, EntityManagerInterface $em, CategoryRepository $categoryRepo): Response
    {
        $product = $repo->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setPrice((float)$request->request->get('price'));
            
            $categoryId = $request->request->get('category_id');
            if ($categoryId) {
                $category = $categoryRepo->find($categoryId);
                $product->setCategory($category);
            } else {
                $product->setCategory(null);
            }
            
            $em->flush();
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/form.html.twig', [
            'product' => $product,
            'categories' => $categoryRepo->findAll(),
        ]);
    }

    #[Route('/products/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(int $id, ProductRepository $repo, EntityManagerInterface $em): Response
    {
        $product = $repo->find($id);
        if ($product) {
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('admin_products');
    }

    // Categories
    #[Route('/categories', name: 'admin_categories')]
    public function categories(CategoryRepository $repo): Response
    {
        return $this->render('admin/categories/list.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    #[Route('/categories/new', name: 'admin_category_new', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $category = new Category();
            $category->setName($request->request->get('name'));
            
            $em->persist($category);
            $em->flush();
            
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/form.html.twig', [
            'category' => null,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(int $id, Request $request, CategoryRepository $repo, EntityManagerInterface $em): Response
    {
        $category = $repo->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        if ($request->isMethod('POST')) {
            $category->setName($request->request->get('name'));
            $em->flush();
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/form.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function deleteCategory(int $id, CategoryRepository $repo, EntityManagerInterface $em): Response
    {
        $category = $repo->find($id);
        if ($category) {
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_categories');
    }

    // Orders
    #[Route('/orders', name: 'admin_orders')]
    public function orders(OrderRepository $repo): Response
    {
        return $this->render('admin/orders/list.html.twig', [
            'orders' => $repo->findAll(),
        ]);
    }

    #[Route('/orders/new', name: 'admin_order_new', methods: ['GET', 'POST'])]
    public function newOrder(Request $request, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        if ($request->isMethod('POST')) {
            $order = new Order();
            $dateInput = $request->request->get('date');
            $date = $dateInput ? \DateTime::createFromFormat('Y-m-d\\TH:i', $dateInput) : null;
            $order->setDate($date ?: new \DateTime());
            $order->setTotal((float)$request->request->get('total'));

            $productIds = $request->request->all('product_ids');
            if (!is_array($productIds)) {
                $productIds = $productIds ? [$productIds] : [];
            }

            foreach ($productIds as $productId) {
                $product = $productRepo->find($productId);
                if ($product) {
                    $order->addProduct($product);
                }
            }

            $em->persist($order);
            $em->flush();
            
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/orders/form.html.twig', [
            'order' => null,
            'products' => $productRepo->findAll(),
        ]);
    }

    #[Route('/orders/{id}/edit', name: 'admin_order_edit', methods: ['GET', 'POST'])]
    public function editOrder(int $id, Request $request, OrderRepository $repo, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        $order = $repo->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        if ($request->isMethod('POST')) {
            $dateInput = $request->request->get('date');
            if ($dateInput) {
                $date = \DateTime::createFromFormat('Y-m-d\\TH:i', $dateInput);
                if ($date) {
                    $order->setDate($date);
                }
            }

            $order->setTotal((float)$request->request->get('total'));

            foreach ($order->getProducts()->toArray() as $product) {
                $order->removeProduct($product);
            }

            $productIds = $request->request->all('product_ids');
            if (!is_array($productIds)) {
                $productIds = $productIds ? [$productIds] : [];
            }

            foreach ($productIds as $productId) {
                $product = $productRepo->find($productId);
                if ($product) {
                    $order->addProduct($product);
                }
            }

            $em->flush();
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('admin/orders/form.html.twig', [
            'order' => $order,
            'products' => $productRepo->findAll(),
        ]);
    }

    #[Route('/orders/{id}/delete', name: 'admin_order_delete', methods: ['POST'])]
    public function deleteOrder(int $id, OrderRepository $repo, EntityManagerInterface $em): Response
    {
        $order = $repo->find($id);
        if ($order) {
            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('admin_orders');
    }
}
