<?php

namespace App\Controller;

use App\Entity\PasswordRecovery;
use App\Repository\PasswordRecoveryRepository;
use App\Repository\UserRepository;
use App\Services\SendEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /*
    user enters email
    when submits, generate random token based on time
    this token is then hashed and stored in db with user id and expiry time
    send user email with link to reset containing hashed token
    user clicks link, page take hashed token searches table for a user
    if token checks out, redirect to reset page containing set new password form
    user enters new password and is redirected to login
    email sent to user stating password has been changed
    */
    /**
     * @Route("/recovery", name="app_recovery")
     */
    public function recovery(
        Request $request,
        UserRepository $userRepository,
        SendEmail $sendEmail
    ) {
        $form = $this->createFormBuilder()
            ->add(
                'email',
                RepeatedType::class,
                [
                    'required' => true,
                    'first_options' => ['label' => 'Email'],
                    'second_options' => ['label' => 'Confirm Email']
                ]
            )
            ->add('Reset_Password', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $recoverEntity = new PasswordRecovery();
            $data = $form->getData();
            $userEmail = $data['email'];
            $user = $userRepository->findOneBy(['email' => $userEmail]);
            $time = new \DateTime();
            $token = md5($time->format('u'));
            $recoverEntity->setToken($token);
            $recoverEntity->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($recoverEntity);
            $em->flush();
            $sendEmail->sendRecoveryEmail($data['email'], $user->getFirstName(), $token);
            return$this->redirectToRoute('app_login');
        }


        return $this->render(
            'recovery/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function getToken(
        Request $request,
        $token,
        PasswordRecoveryRepository $passwordRecoveryRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $userPasswordEncoderInterface
    ) {
        $tokenEntity = $passwordRecoveryRepository->findOneBy(['token' => $token]);
        if (!$tokenEntity) {
            return $this->render(
                'errors/reset.html.twig',
                [
                    'error' => 'This link has expired'
                ]
            );
        }
        $form = $this->createFormBuilder()
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'New Password'],
                    'second_options' => ['label' => 'Confirm Password']
                ]
            )
            ->add('Reset_Password', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $userId = $tokenEntity->getUser()->getId();
            $data = $form->getData();
            $user = $userRepository->findOneBy(['id' => $userId]);
            $user->setPassword(
                $userPasswordEncoderInterface->encodePassword($user, $data['password'])
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->remove($tokenEntity);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'recovery/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
