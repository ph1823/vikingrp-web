<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\SkinUpload;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/profile', name: 'user_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
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
        }

        return $this->renderBase('user/dashboard.html.twig', [
            'skinForm' => $form->createView()
        ]);
    }

}
