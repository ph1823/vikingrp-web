<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\WikiCategory;
use App\Entity\WikiPage;
use App\Form\SkinUpload;
use App\Repository\ArticleRepository;
use App\Repository\WikiCategoryRepository;
use App\Repository\WikiPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
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


    #[NoReturn] #[Route('/wiki/{cat_url?}/{url?}', name: 'wiki_cat')]
    public function wiki_cat(?string $cat_url, ?string $url, WikiCategoryRepository $wikiCategoryRepository, WikiPageRepository $wikiPageRepository): Response
    {
        /** @var ?WikiCategory $wikiCategory */
        $wikiCategory = $cat_url ? $wikiCategoryRepository->findOneBy(["cat_url" => $cat_url]) : null;
        $wikiPage = !$wikiCategory ? $wikiPageRepository->findOneBy(["url" => $cat_url, "wikiCategory" => null]) : null;
        $list = [];
        if($url != null)
            $wikiPage = $wikiCategory->getPages()->findFirst(function (int $index, WikiPage $page) use ($url) {
                return $page->getUrl() === $url;
            });

        //if we are on main page, we generate a list of cat / wikiaricle without cat
        if($url == null && $wikiCategory == null) {
            $list = array_map(function(WikiCategory $cat) {
                return ["icon" => $cat, "name" => $cat->getCatUrl(),
                    "iconName" => "catImage", "className" => $cat::class,
                    "url" => $this->generateUrl('wiki_cat', ["cat_url" =>  $cat->getCatUrl()]),
                    "iconShow" => $cat->getCatImageName() != null];
            }, $wikiCategoryRepository->findAll());
            //
            $list = array_merge($list, array_map(function(WikiPage $page) {
                return ["icon" => $page,
                    "iconShow" => $page->getIconImageName() != null,
                    "name" => $page->getUrl(),
                    "url" => $this->generateUrl('wiki_cat', ["cat_url" =>  $page->getUrl()]),
                    "iconName" => "iconImage",
                    "className" => $page::class];
            }, $wikiPageRepository->findBy(['wikiCategory' => null])));
        }

        return $this->renderBase('wiki.html.twig', [
            'wiki' => $wikiPage,
            'cats' => $wikiCategory?->getPages()->toArray(),
            'list' => $list
        ]);
    }

}
