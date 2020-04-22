<?php

namespace App\Controller;

use App\Repository\LeagueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SearchLeagueController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function index(LeagueRepository $leagueRepository)
    {
        $leagues = $leagueRepository->findAll();
        return $this->render(
            'search_league/index.html.twig',
            [
                'leagues' => $leagues
            ]
        );
    }
}
