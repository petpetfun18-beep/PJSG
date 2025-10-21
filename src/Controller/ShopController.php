<?php

namespace App\Controller;

use App\Entity\Shoe;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop', name: 'app_shop_')]
class ShopController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Fetch categories from database
        $categories = $em->getRepository(Category::class)->findAll();

        // ✅ Check if "All" already exists in DB
        $hasAll = false;
        foreach ($categories as $cat) {
            if (strtolower($cat->getName()) === 'all') {
                $hasAll = true;
                break;
            }
        }

        // ✅ Only add "All" manually if it's not in DB
        if (!$hasAll) {
            $allCategory = new Category();
            $allCategory->setName('All');
            array_unshift($categories, $allCategory);
        }

        // Selected category from ?category= parameter
        $selectedCategory = $request->query->get('category', 'All');

        $shoeRepo = $em->getRepository(Shoe::class);

        // ✅ Filter by string category
        if (!empty($selectedCategory) && strtolower($selectedCategory) !== 'all') {
            $shoes = $shoeRepo->createQueryBuilder('s')
                ->where('s.category = :categoryName')
                ->setParameter('categoryName', $selectedCategory)
                ->getQuery()
                ->getResult();
        } else {
            $shoes = $shoeRepo->findAll();
        }

        return $this->render('shop/index.html.twig', [
            'shoes' => $shoes,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Shoe $shoe): Response
    {
        return $this->render('shop/show.html.twig', [
            'shoe' => $shoe,
        ]);
    }
}
