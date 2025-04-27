<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserAPI extends AbstractController
{
    #[Route('/api/user', name: 'user_update', methods: 'PUT')]
    public function updateUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
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
        $user->setPassword($passwordHasher->hashPassword($user, $userData->newPassword));
        $user->setMail($user->getMail());
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json("");
    }

    #[Route('/api/user/login', methods: 'POST')]
    public function loginUser(Request $request, UserRepository $user, UserPasswordHasherInterface $passwordHasher) {
        $content = json_decode($request->getContent());
        $user = $user->find($content->username || "");
        $password = $content->password || "";

        if(!$user) return $this->json(["message" => "Use not found"], 404);
        if(!$password)return $this->json(["message" => "No password"], 404);

        if(!$passwordHasher->isPasswordValid($user, $password)) return $this->json(["message" => "Use not found"], Response::HTTP_FORBIDDEN);

        return $this->json(["message" => "ok"]);
    }
}