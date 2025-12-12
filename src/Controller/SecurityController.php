<?php

namespace App\Controller;

use App\Repository\ShoeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        if ($user) {
            // Redirect based on roles
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_STAFF')) {
                return $this->redirectToRoute('admin_dashboard');
            }

            // Normal users go to home
            return $this->redirectToRoute('app_home');
        }

        // Not logged in, show login form
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername() ?? '';

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(ShoeRepository $shoeRepository): Response
    {
        // Only allow admin or staff
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_STAFF')) {
            return $this->redirectToRoute('app_home');
        }

        $shoes = $shoeRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'shoes' => $shoes,
        ]);
    }
}
