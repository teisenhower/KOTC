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
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;

class RegisterController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, LeagueRepository $leagueRepository, UserRepository $userRepository)
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
            // checks if email exists
            if ($userRepository->findOneBy(['email' => $data['email']])) {
                $error = new FormError('Email Already Exists');
                $form->get('email')->addError($error);
                return $this->render(
                    'register/index.html.twig',
                    [
                        'form' => $form->createView()
                    ]
                );
            }
            // checks if username exists
            if ($userRepository->findOneBy(['username' => $data['username']])) {
                $error = new FormError('Username Already Exists');
                $form->get('username')->addError($error);
                return $this->render(
                    'register/index.html.twig',
                    [
                        'form' => $form->createView()
                    ]
                );
            }
            // gets league from league table based on ref code given
            $league = $leagueRepository->findOneBy(['refCode' => $data['refCode']]);
            // checks if league exists
            if (!$league) {
                $error = new FormError('Invalid Ref Code');
                $form->get('refCode')->addError($error);
                return $this->render(
                    'register/index.html.twig',
                    [
                        'form' => $form->createView()
                    ]
                );
            }
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);

            $user->setLeagueName($league->getLeagueName());
            $user->setLeague($league);
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
