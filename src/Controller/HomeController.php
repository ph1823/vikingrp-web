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
use Symfony\Component\Routing\Attribute\Route;

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
}
