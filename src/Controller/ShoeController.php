<?php

namespace App\Controller;

use App\Entity\Shoe;
use App\Form\ShoeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\ActivityLogger;

#[Route('/admin/shoe', name: 'app_admin_shoe')]
class ShoeController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em, ActivityLogger $acitivityLogger): Response
    {
        $shoe = new Shoe();
        $form = $this->createForm(ShoeType::class, $shoe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ Convert comma-separated sizes to array
            $sizes = $form->get('sizes')->getData();
            if (is_string($sizes)) {
                $shoe->setSizes(array_filter(array_map('trim', explode(',', $sizes))));
            }

            // ✅ Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed.');
                }

                $shoe->setImage($newFilename);
            }

            // ✅ Category is automatically handled by EntityType — no need to convert
            $em->persist($shoe);
            $em->flush();

            $acitivityLogger->log('Added new shoe: ' . $shoe->getId());

            $this->addFlash('success', 'Shoe added successfully!');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/shoe/{id}', name: 'app_shoe_show')]
    public function show(Shoe $shoe): Response
    {
        return $this->render('shop/show.html.twig', [
            'shoe' => $shoe,
        ]);
    }
}
