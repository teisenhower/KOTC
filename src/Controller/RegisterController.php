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
use App\Services\SendEmail;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class RegisterController extends AbstractController
{
    /**
     * @Route("/signup", name="app_signup")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        LeagueRepository $leagueRepository,
        UserRepository $userRepository,
        SendEmail $sendEmail
    ) {
        $form = $this->createFormBuilder()
            ->add('firstName', null, ['error_bubbling' => true,])
            ->add('lastName', null, ['error_bubbling' => true,])
            ->add('username', null, ['error_bubbling' => true,])
            ->add(
                'email',
                null,
                [
                    'error_bubbling' => true,
                    'constraints' => [
                        new Email(
                            [
                                'message' => 'Please enter a valid email address: {{ value }} is invalid'
                            ]
                        )
                    ]
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'error_bubbling' => true,
                    'constraints' => [
                        new Length(
                            [
                                'min' => 8,
                                'minMessage' => 'Password must be at least 8 characters'
                            ]
                        ),
                    ],
                    'invalid_message' => 'Passwords do not match',
                ]
            )
            ->add('refCode', null, ['error_bubbling' => true,])
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
            if ($form->isValid()) {
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
                $sendEmail->sendRegistrationEmail($data['email'], $data['firstName'], $data['lastName']);
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render(
            'register/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
