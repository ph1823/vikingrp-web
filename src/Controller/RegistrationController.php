<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends BaseController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
            $recaptcha_response = $request->get("g-recaptcha-response");
            $recaptcha_secret = $_ENV['RECAPTCHA_SECRET_KEY'] ;

            // Make and decode POST request:
            $recaptcha = file_get_contents("$recaptcha_url?secret=$recaptcha_secret&response=$recaptcha_response");
            $recaptcha = json_decode($recaptcha, null, 512, JSON_THROW_ON_ERROR);

            if (isset($recaptcha->score) && $recaptcha->score > 0.5) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('app_home');
            } else {
                $form->addError(new FormError("Captcha Invalide."));
            }
        }

        return $this->renderBase('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
}
