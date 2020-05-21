<?php

namespace App\Controller;

use App\Entity\Motto;
use App\Form\MottoType;
use App\Repository\MottoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{username}", name="app_profile")
     */
    public function index(
        $username,
        Request $request,
        MottoRepository $mottoRepository
    ) {
        $user = $this->getUser();
        // retrieve players motto
        $motto = $mottoRepository->findOneBy(
            ['player' => $user->getId()]
        );
        // checks if a motto exists. if not we create a new Motto object
        if (!$motto) {
            $motto = new Motto;
        }
        $form = $this->createForm(
            MottoType::class,
            $motto
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $motto->setPlayer($user);
            $em = $this->getDoctrine()->getManager();
            $em->merge($motto);
            $em->flush();
            return $this->redirectToRoute('app_profile', ['username' => $username]);
        }
        return $this->render(
            'profile/index.html.twig',
            [
                'user' => $user,
                'username' => $username,
                'form' => $form->createView()
            ]
        );
    }
}
