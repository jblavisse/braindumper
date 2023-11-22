<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthentificatorAuthenticator;
use App\Service\JWTService;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, JWTService $JWTService, SendEmailService $mail, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthentificatorAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $registrationForm->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId(),
            ];

            $token = $JWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            try {
                $mail->send(
                    'noreply@example.com',
                    $user->getEmail(),
                    'Activation de votre compte sur le site Vide-cerveau !',
                    'register_email',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                );

                return $this->redirectToRoute('app_login');
            } catch (TransportExceptionInterface $exception) {
                $this->addFlash('error', "Une erreur est survenue lors de l'envoie de l'email d'activation.");
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
        ]);
    }


    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(
        $token,
        JWTService $jwt,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {

        if ($jwt->isValid($token) && $jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload['user_id']);

            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(true);
                $user -> getIsVerified();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre email a bien été vérifié, votre compte est activée');
                return $this->redirectToRoute('app_memory');
            }

            $this->addFlash('error', 'Le token est invalide ou invalid. ');
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_memory');
    }


    #[Route('/resendverif', name: 'resend_verif')]
    public function resendVerif(SendEmailService $mail, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('error', 'Cet utilisateur est déjà activé.');
            return $this->redirectToRoute('app_memory');
        }
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $user->getId(),
        ];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send(
            'noreply@example.com',
            $user->getEmail(),
            'Activation de votre compte sur le site Vide-cerveau !',
            'register_email',
            [
                'user' => $user,
                'token' => $token,
            ]
        );
        return $this->redirectToRoute('app_memory');
    }
}