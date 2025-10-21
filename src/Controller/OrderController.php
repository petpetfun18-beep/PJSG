<?php
// src/Controller/OrderController.php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\Shoe;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/now/{id}', name: 'app_order_now', methods: ['GET'])]
    public function orderNow(Shoe $shoe): Response
    {
        return $this->render('order/order_now.html.twig', [
            'shoe' => $shoe,
        ]);
    }

    #[Route('/order/submit/{id}', name: 'app_order_submit', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $entityManager, Shoe $shoe): Response
    {
        $order = new Order();
        $order->setCustomerName($request->request->get('customer_name') ?? 'No name provided');
        $order->setCustomerPhone($request->request->get('customer_phone') ?? 'No phone provided');
        $order->setCustomerAddress($request->request->get('customer_address') ?? 'No address provided');

        $order->setShoe($shoe);
        $order->setTotalAmount($shoe->getPrice());
        $order->setOrderDate(new \DateTimeImmutable());
        $order->setStatus('Pending');

        $selectedColor = $request->request->get('color');
        $selectedSize = $request->request->get('size');
        $paymentMethod = $request->request->get('payment_method') ?? 'Cash on Delivery';

        $order->setSelectedColor($selectedColor);
        $order->setSelectedSize($selectedSize);
        $order->setPaymentMethod($paymentMethod);
        $order->setItems([
            'color' => $selectedColor,
            'size' => $selectedSize,
            'payment_method' => $paymentMethod,
        ]);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->render('order/order_success.html.twig', [
            'shoe' => $shoe,
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_view', methods: ['GET'])]
    public function view(Order $order): Response
    {
        return $this->render('order/view.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/edit', name: 'app_order_edit', methods: ['GET','POST'])]
    public function edit(Order $order, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OrderType::class, $order); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Order updated successfully!');
            return $this->redirectToRoute('app_admin_orders');
        }

        return $this->render('admin/order_edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    // ─── Update Order Status ───────────────────────
    #[Route('/orders/{id}/status/{status}', name: 'app_admin_orders_update')]
    public function updateOrderStatus(Order $order, string $status, EntityManagerInterface $em): Response
    {
        $order->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Order status updated!');
        return $this->redirectToRoute('app_admin_orders');
    }
}
