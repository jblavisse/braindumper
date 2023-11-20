<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $response = $this->redirectToRoute('app_login');
        $response->send();
    }

    #[Route('/forgottenpassword', name: 'forgotten_password')]
    public function forgottenPassword(Request $request, ResetPasswordRequestFormType $requestForm, EntityManagerInterface $entityManager, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, SendEmailService $mail): Response
    {
        $requestForm = $this->createForm(ResetPasswordRequestFormType::class);
        $requestForm->handleRequest($request);

        if ($requestForm->isSubmitted() && $requestForm->isValid()) {
            $user = $userRepository->findOneByEmail($requestForm->get('email')->getData());

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $context = [
                    'url' => $url,
                    'user' => $user,
                ];

                $mail->send(
                    'noreply@exemple.com',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès !');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/request.html.twig', ['requestForm' => $requestForm->createView()]);

    }

    #[Route('/forgottenpassword/{token}', name: 'reset_password')]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        string $token
    ): Response {
        $user = $userRepository->findOneByResetToken($token);

        if (!$user) {
            $this->addFlash('error', 'Jeton invalide');
            return $this->redirectToRoute('app_login');
        }

        $resetForm = $this->createForm(ResetPasswordFormType::class);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            dump($resetForm->getErrors(true, false));
            $user->setResetToken('');
            $user->setPasswordHash(
                $passwordHasher->hashPassword(
                    $user,
                    $resetForm->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe changé avec succès ! ');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', ['resetForm' => $resetForm->createView()]);
    }
}