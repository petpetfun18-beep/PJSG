<?php

namespace App\Controller;

use App\Entity\Shoe;
use App\Entity\Order; 
use App\Form\ShoeType;
use App\Repository\ShoeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class AdminController extends AbstractController
{
    // ─── Dashboard: List all shoes ─────────────────────
    #[Route('/', name: 'app_admin_dashboard')]
    #[Route('/index', name: 'app_admin_index')] // ✅ Added alias route
    public function index(ShoeRepository $shoeRepository): Response
    {
        $shoes = $shoeRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'shoes' => $shoes,
        ]);
    }

    // ─── Add new shoe ────────────────────────────────
    #[Route('/new', name: 'app_admin_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $shoe = new Shoe();
        $form = $this->createForm(ShoeType::class, $shoe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
                }

                $shoe->setImage($newFilename);
            }

            $em->persist($shoe);
            $em->flush();

            $this->addFlash('success', 'Shoe added successfully!');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_edit')]
    public function edit(Shoe $shoe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ShoeType::class, $shoe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Delete old image if exists
                    if ($shoe->getImage()) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $shoe->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $shoe->setImage($newFilename);

                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed: ' . $e->getMessage());
                }
            }

            $em->flush();
            $this->addFlash('success', 'Shoe updated successfully!');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'shoe' => $shoe,
        ]);
    }

    // ─── Delete shoe ────────────────────────────────
    #[Route('/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Shoe $shoe, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shoe->getId(), $request->request->get('_token'))) {
            $orders = $em->getRepository(Order::class)->findBy(['shoe' => $shoe]);
            if (count($orders) > 0) {
                $this->addFlash('error', 'Cannot delete this shoe because there are orders associated with it.');
                return $this->redirectToRoute('app_admin_dashboard');
            }

            if ($shoe->getImage()) {
                $oldImagePath = $this->getParameter('images_directory') . '/' . $shoe->getImage();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $em->remove($shoe);
            $em->flush();
            $this->addFlash('success', 'Shoe deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    // ─── List all orders ─────────────────────────────
    #[Route('/orders', name: 'app_admin_orders')]
    public function orders(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(Order::class)->findAll();

        $totalOrders = count($orders);
        $pendingOrders = count(array_filter($orders, fn($order) => $order->getStatus() === 'Pending'));
        $completedOrders = count(array_filter($orders, fn($order) => $order->getStatus() === 'Completed'));
        $cancelledOrders = count(array_filter($orders, fn($order) => $order->getStatus() === 'Cancelled'));

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
        ]);
    }

    // ─── Update order status ─────────────────────────
    #[Route('/orders/{id}/status/{status}', name: 'app_admin_orders_update')]
    public function updateOrderStatus(Order $order, string $status, EntityManagerInterface $em): Response
    {
        $order->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Order status updated!');
        return $this->redirectToRoute('app_admin_orders');
    }
}
