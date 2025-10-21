<?php
namespace App\Controller;

use App\Entity\Shoe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index')]
    public function index(Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);
        return $this->render('cart/index.html.twig', ['cart' => $cart]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Request $request, Shoe $shoe): Response
    {
        $cart = $request->getSession()->get('cart', []);
        $id = $shoe->getId();

        if (!isset($cart[$id])) {
            $cart[$id] = [
                'id' => $shoe->getId(),
                'name' => $shoe->getName(),
                'price' => $shoe->getPrice(),
                'image' => $shoe->getImage(),
                'quantity' => 1,
            ];
        } else {
            $cart[$id]['quantity']++;
        }

        $request->getSession()->set('cart', $cart);
        $this->addFlash('success', "{$shoe->getName()} added to cart!");

        return $this->redirectToRoute('app_shop_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(Request $request, int $id): Response
    {
        $cart = $request->getSession()->get('cart', []);
        unset($cart[$id]);
        $request->getSession()->set('cart', $cart);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(Request $request): Response
    {
        $request->getSession()->remove('cart');
        return $this->redirectToRoute('app_cart_index');
    }
}
