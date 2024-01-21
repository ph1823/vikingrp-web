<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\SkinUpload;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use function Symfony\Component\String\u;

class HomeController extends BaseController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->renderBase('home.html.twig', ['articles' => $articleRepository->findAll()]);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function article(Article $article): Response
    {
        return $this->renderBase('home.html.twig', ['article' => $article]);
    }


    #[Route('/skin', name: 'skin_main')]
    public function skin(Request $request, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        /** @var ?User $user */
        $user = $this->getUser();

        if(!$user) $this->redirectToRoute("app_home");
        //else $this->render('home.html.twig');

        $form = $this->createForm(SkinUpload::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$user->setImageFile($form->getData()['skinImage']);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->renderBase('skin/upload.html.twig', [
            'skinForm' => $form->createView()
        ]);
    }

    #[Route('/skin/create', name: 'skin_create')]
    public function createSkin()
    {

    }

    #[Route('/profile', name: 'user_profile')]
    public function profile(): Response
    {

        return $this->renderBase('user/dashboard.html.twig');
    }

    #[Route('/api/user', name: 'user_update', methods: 'PUT')]
    public function updateUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager) {
        $userData = json_decode($request->getContent());
        $errors = [];

        // Check if data is not empty
        if(!$userData->email) $errors["email"] = "L'e-mail ne peut pas être vide.";
        if(!$userData->username) $errors["username"] = "Le nom d'utilisateur ne peut pas être vide.";
        if(!$userData->newPassword && $userData->password) $errors["password"] = "Le mot de passe ne peut pas être vide.";
        if($userData->newPassword && !$userData->password) $errors["newPassword"] = "Le nouveau mot de passe ne peut pas être vide.";

        if(!empty($errors))
            return $this->json($errors, 400);

        // Check if data is valid
        $usernameRegex = "/^[a-zA-Z0-9]+$/";
        if(!preg_match($usernameRegex, $userData->username)) $errors["username"] = "Le nom d'utilisateur ne doit pas contenir de caractère spéciaux.";
        if(!filter_var($userData->email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "L'email doit être au format email";
        if(!empty($userData->newPassword) && $userData->newPassword !== $userData->newConfirmPassword) $errors["newPassword"] = "Les deux mots de passe ne correspondent pas.";

        if(!empty($errors))
            return $this->json($errors, 400);


        /** @var User $user */
        $user = $this->getUser();
        if(!$user) return $this->json(["message" => "Utlisateur non connectée."], 401);
        if(!$passwordHasher->isPasswordValid($user, $userData->password)) $errors["password"] = "Mot de passe incorect";
        if(!empty($errors))
            return $this->json($errors, 400);

        $user->setUsername($userData->username);
        $user->setPassword($userData->newPassword);
        $user->setMail($user->getMail());
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json("");
    }
}
