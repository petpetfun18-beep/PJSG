<?php
namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/checkout')]
class CheckoutController extends AbstractController
{
    #[Route('/', name: 'app_checkout')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $cart = $request->getSession()->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('warning', 'Your cart is empty!');
            return $this->redirectToRoute('app_cart_index');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $address = $request->request->get('address');
            $payment = $request->request->get('payment');

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $order = new Order();
            $order->setCustomerName($name);
            $order->setCustomerEmail($email);
            $order->setCustomerAddress($address);
            $order->setPaymentMethod($payment);
            $order->setTotalAmount($total);
            $order->setOrderDate(new \DateTimeImmutable());
            $order->setItems($cart);
            $order->setStatus('Pending');

            $em->persist($order);
            $em->flush();

            $request->getSession()->remove('cart');
            $this->addFlash('success', 'Order placed successfully!');

            return $this->redirectToRoute('app_shop_index');
        }

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
        ]);
    }
}
