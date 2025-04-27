<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{
    public function renderBase(string $view, array $parameters = []): Response
    {
        $parameters["playerOnline"] = 0;
        return $this->render($view, $parameters);
    }
}
