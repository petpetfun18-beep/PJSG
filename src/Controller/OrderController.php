<?php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\Shoe;
use App\Form\OrderType;
use App\Service\ActivityLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private ActivityLogger $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    #[Route('/order/now/{id}', name: 'app_order_now', methods: ['GET'])]
    public function orderNow(Shoe $shoe): Response
    {
        $activityLogger->log('Opened order form for shoe: ' . $shoe->getName());

        return $this->render('order/order_now.html.twig', [
            'shoe' => $shoe,
        ]);
    }

    #[Route('/order/submit/{id}', name: 'app_order_submit', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $entityManager, Shoe $shoe, ActivityLogger $activityLogger): Response
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

        $activityLogger->log(sprintf(
            'New order placed by %s for shoe "%s" (Size: %s, Color: %s, Payment: %s)',
            $order->getCustomerName(),
            $shoe->getName(),
            $selectedSize,
            $selectedColor,
            $paymentMethod
        ));

        return $this->render('order/order_success.html.twig', [
            'shoe' => $shoe,
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_view', methods: ['GET'])]
    public function view(Order $order): Response
    {
        $activityLogger->log('Viewed order ID: ' . $order->getId());

        return $this->render('order/view.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/edit', name: 'app_order_edit', methods: ['GET','POST'])]
    public function edit(Order $order, Request $request, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
    {
        $form = $this->createForm(OrderType::class, $order); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $activityLogger->log('Edited order ID: ' . $order->getId());

            $this->addFlash('success', 'Order updated successfully!');
            return $this->redirectToRoute('app_admin_orders');
        }

        return $this->render('admin/order_edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    #[Route('/orders/{id}/status/{status}', name: 'app_admin_orders_update')]
    public function updateOrderStatus(Order $order, string $status, EntityManagerInterface $em, ActivityLogger $activityLogger): Response
    {
        $order->setStatus($status);
        $em->flush();

        $activityLogger->log(sprintf(
            'Order ID %d status changed to "%s"',
            $order->getId(),
            $status
        ));

        $this->addFlash('success', 'Order status updated!');
        return $this->redirectToRoute('app_admin_orders');
    }
}
