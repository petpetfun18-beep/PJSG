<?php

namespace App\Controller;

use App\Repository\ActivityLogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity-logs')]
class ActivityLogsController extends AbstractController
{
    #[Route('/', name: 'app_activity_logs')]
    public function index(ActivityLogsRepository $activityLogsRepository): Response
    {
       $logs = $activityLogs = $activityLogsRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('activity_logs/index.html.twig', [
            'controller_name' => 'ActivityLogsController',
            'logs' => $logs,
        ]);
    }
}
