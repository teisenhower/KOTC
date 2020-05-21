<?php

namespace App\Controller;

use App\Form\AddScoreType;
use App\Entity\Stats;
use App\Repository\StatsRepository;
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
            return $this->submitScore($stats, $user);
        };
        return $this->render(
            'add_score/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * @Route("/edit_score/{id}", name="app_edit_score")
     */
    public function edit(Request $request, $id, StatsRepository $statsRepository)
    {
        $stats = $statsRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();
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
            return $this->submitScore($stats, $user);
        };

        return $this->render(
            'add_score/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * @Route("/delete/{id}" , name="app_delete_score")
     */
    public function delete(StatsRepository $statsRepository, $id)
    {
        $stat = $statsRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->remove($stat);
        $em->flush();
        return $this->redirectToRoute('app_user', ['username' => $user]);
    }

    public function submitScore($stats, $user)
    {
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
        return $this->redirectToRoute('app_user', ['username' => $user]);
    }
}
