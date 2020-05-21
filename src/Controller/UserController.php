<?php

namespace App\Controller;

use App\Repository\MottoRepository;
use App\Repository\StatsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{username}", name="app_user")
     */
    public function index(
        Request $request,
        $username,
        StatsRepository $statsRepository,
        UserRepository $userRepository,
        MottoRepository $mottoRepository
    ) {
        $currentUser = $this->getUser();
        $user = $userRepository->findOneBy(['username' => $username]);
        $playerMotto = $mottoRepository->findMotto($user->getId())['motto'];

        $pointsBreakdown = $statsRepository->getBreakdown($user->getId());
        $hitHistory = $statsRepository->findBy(
            ['player' => $user->getId()],
            ['date' => 'DESC'],
            10
        );
        $topTargets = $statsRepository->getTopTargets($user->getId());
        $types = ['Assist', 'Battle', 'Stealth'];
        foreach ($types as $type) {
            if (!array_key_exists($type, $pointsBreakdown)) {
                $pointsBreakdown += [$type => '0'];
            }
        }
        $username = $request->get('username');

        return $this->render(
            'user/index.html.twig',
            [
                'currentUser' => $currentUser,
                'username' => $username,
                'motto' => $playerMotto,
                'totals' => $pointsBreakdown,
                'hitHistory' => $hitHistory,
                'topTargets' => $topTargets,
            ]
        );
    }
}
