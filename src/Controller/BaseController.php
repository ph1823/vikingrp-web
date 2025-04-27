<?php

namespace App\Controller;

use PHPMinecraft\MinecraftQuery\Exception\MinecraftQueryException;
use PHPMinecraft\MinecraftQuery\MinecraftQueryResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{
    /**
     * @throws MinecraftQueryException
     */
    public function renderBase(string $view, array $parameters = []): Response
    {
        $resolver = new MinecraftQueryResolver('91.197.6.83', 25573);
        $parameters["playerOnline"] = $resolver->getResult()->getOnlinePlayers();
        return $this->render($view, $parameters);
    }
}
