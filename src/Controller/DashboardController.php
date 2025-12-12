<?php

namespace App\Controller;

use App\Repository\ActivityLogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(ActivityLogsRepository $activityLogsRepository): Response
    {
        $activityLogs = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $activityLogs = $activityLogsRepository->findBy([], ['createdAt' => 'DESC'], 7);
        }

        return $this->render('dashboard/index.html.twig', [
            'activityLogs' => $activityLogs,
        ]);
    }
}
