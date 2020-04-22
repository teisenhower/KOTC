<?php

namespace App\Controller;

use App\Form\AddScoreType;
use App\Entity\Stats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddScoreController extends AbstractController
{
    /**
     * @Route("/add_score", name="app_add_score")
     */
    public function index(Request $request)
    {
        $stats = new Stats();
        $user = $this->getUser();
        $league = $user->getLeagueName();
        $form = $this->createForm(
            AddScoreType::class,
            $stats,
            [
                // passes current user object to form
                'current_user' => $user
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $pointValues = [
                'Battle' => 1.0,
                'Stealth' => 2.0,
                'Assist' => .5
            ];
            $stats->setPlayer($user);
            $stats->setPoints($pointValues[$stats->getType()]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($stats);
            $em->flush();
            return $this->redirectToRoute('app_league', ['league_name' => $league]);
        }
        return $this->render(
            'add_score/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
