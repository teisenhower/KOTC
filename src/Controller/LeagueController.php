<?php

namespace App\Controller;

use App\Repository\LeagueRepository;
use App\Repository\StatsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LeagueController extends AbstractController
{
    /**
     * @Route("/league/{league_name}", name="app_league")
     */
    public function index(Request $request, LeagueRepository $leagueRepository, UserRepository $userRepository, StatsRepository $statsRepository)
    {
        $league = $request->get('league_name');
        // get league ID
        $leagueOBJ = $leagueRepository->findOneBy(
            ['leagueName' => $league]
        );
        $leaugeID = $leagueOBJ->getId();

        // get all users in that league
        $users = $userRepository->findBy(['league' => $leaugeID]);
        
        // tally up player scores
        foreach ($users as $user) {
            $total = 0;
            $playerStats = $statsRepository->findBy(['player' => $user->getId()]);
            foreach ($playerStats as $stat) {
                $total += $stat->getPoints();
            }
            // add total property to user object so I can call it in the template
            $user->{'total'} = $total;
        };
        // sorting users from highest to lowest score
        usort(
            $users,
            function ($a, $b) {
                if ($a->total == $b->total) {
                    return 0;
                }
                return ($a->total < $b->total) ? 1 : -1;
            }
        );
        return $this->render(
            'league/index.html.twig',
            [
                'league' => $league,
                'users' => $users,
            ]
        );
    }
}
