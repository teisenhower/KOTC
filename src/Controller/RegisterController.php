<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\LeagueRepository;

class RegisterController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, LeagueRepository $leagueRepository)
    {
        $form = $this->createFormBuilder()
            ->add('firstName')
            ->add('lastName')
            ->add('username')
            ->add('email')
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                ]
            )
            ->add('refCode')
            ->add('Sign_Up', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            // get league name from league table
            $league = $leagueRepository->findOneBy(['refCode' => $data['refCode']]);
            $user->setLeagueID($league->getId());
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $data['password'])
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'register/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
