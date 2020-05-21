<?php

namespace App\Controller;

use App\Repository\LeagueRepository;
use App\Repository\MottoRepository;
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
    public function index(
        Request $request,
        LeagueRepository $leagueRepository,
        UserRepository $userRepository,
        StatsRepository $statsRepository,
        MottoRepository $mottoRepository
    ) {
        // $currentUser = $this->getUser()->getUsername();
        $league = $request->get('league_name');
        // get league ID
        $leagueOBJ = $leagueRepository->findOneBy(
            ['leagueName' => $league]
        );
        $leagueID = $leagueOBJ->getId();

        // get all users in that league
        $users = $userRepository->findBy(['league' => $leagueID]);
        
        // tally up player scores
        foreach ($users as $user) {
            $total = 0;
            $playerStats = $statsRepository->findBy(['player' => $user->getId()]);
            $playerMotto = $mottoRepository->findMotto($user->getId())['motto'];
            foreach ($playerStats as $stat) {
                $total += $stat->getPoints();
            }
            /*
            add `total` and `motto` properties to user object
            so I can call them from the league template
            */
            $user->{'total'} = $total;
            $user->{'motto'} = $playerMotto;
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
                // 'currentUser' => $currentUser,
                'league' => $league,
                'users' => $users,
            ]
        );
    }
}
