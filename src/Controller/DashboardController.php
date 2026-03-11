<?php

namespace App\Controller;

use App\Application\Query\GetDashboardQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class DashboardController extends AbstractController
{
    // #[Route('/dashboard', name: 'app_dashboard')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/DashboardController.php',
    //     ]);
    // }

    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 50;

        $envelope = $this->queryBus->dispatch(
            new GetDashboardQuery($page, $perPage)
        );

        $result = $envelope->last(HandledStamp::class)->getResult();
        $total = (int) $result['total'];
        $totalPages = max(1, (int) ceil($total / $perPage));

        return $this->render('dashboard/index.html.twig', [
            'dashboards' => $result['items'],
            'page' => $page,
            'total_pages' => $totalPages
        ]);
    }
}
