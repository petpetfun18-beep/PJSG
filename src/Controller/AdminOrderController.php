<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/orders')]
class AdminOrderController extends AbstractController
{
    // ─── List all orders ───────────────────────
    #[Route('/', name: 'admin_orders_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(Order::class)->findBy([], ['orderDate' => 'DESC']);

        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // ─── View order details ───────────────────
    #[Route('/{id}', name: 'admin_order_view')]
    public function view(Order $order): Response
    {
        return $this->render('admin/orders/view.html.twig', [
            'order' => $order,
        ]);
    }

    // ─── Update order status ──────────────────
    #[Route('/{id}/update', name: 'admin_order_update_status')]
    public function updateStatus(Order $order, EntityManagerInterface $em): Response
    {
        // Cycle status: Pending → Shipped → Delivered
        $statusFlow = ['Pending', 'Shipped', 'Delivered'];
        $currentIndex = array_search($order->getStatus(), $statusFlow);
        $order->setStatus($statusFlow[($currentIndex + 1) % count($statusFlow)]);

        $em->persist($order);
        $em->flush();

        $this->addFlash('success', 'Order status updated to ' . $order->getStatus());

        return $this->redirectToRoute('admin_orders_index');
    }
}
