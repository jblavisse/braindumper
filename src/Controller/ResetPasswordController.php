<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;

class ResetPasswordController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reset/password', name: 'app_reset_password_request')]
    public function requestPasswordReset(Request $request, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $users = $this->$userRepository->findOneBy(['email' => $email]);

            $user = new User();
            $requestForm = $this->createForm(ResetPasswordRequestFormType::class, $user);
            $requestForm->handleRequest($request);

            if ($requestForm->isSubmitted() && $requestForm->isValid()) {
                $user = $requestForm->getData();

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_reset_password_request');

            }

            if ($users) {
                $resetToken = bin2hex(random_bytes(32));

                $users->setResetToken($resetToken);
                $this->entityManager->persist($users);
                $this->entityManager->flush();
                $this->addFlash('success', 'Un e-mail de réinitialisation a été envoyé à votre adresse e-mail.');
            } else {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet e-mail.');
            }
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $requestForm->createView(),
        ]);
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token, UserRepository $userRepository): Response
    {
        $user = $this->$userRepository->findOneBy(['resetToken' => $token]);
        $user = new User();
        $resetForm = $this->createForm(ResetPasswordFormType::class, $user);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $user = $resetForm->getData();

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_reset_password');

        }


        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé pour ce jeton.');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');

            $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($encodedPassword);

            $user->setResetToken(null);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $resetForm->createView(),
        ]);
    }
}