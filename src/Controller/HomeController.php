<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\SkinUpload;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->renderBase('home.html.twig');
    }


    /**
     * @throws OptimisticLockException
     */
    #[Route('/skin', name: 'skin_main')]
    public function skin(Request $request, EntityManagerInterface $entityManager)
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
