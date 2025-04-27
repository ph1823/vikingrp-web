<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/api/skin')]
class SkinAPI extends BaseController
{

    #[Route('/{player}.json', name: 'skin_info')]
    public function skin_info(string $player): JsonResponse
    {
        return $this->json([
            "username" => $player . ".png",
            "cape" => null,
            "elytra" => null,
            "textures" => ["default" => $player . ".png"]
        ]);
    }

    #[Route('/textures/{username}.png', name: 'skin_textures')]
    public function skin_textures(?User $user, DownloadHandler $downloadHandler): Response
    {
        if(!$user) $response = new Response(file_get_contents("images/skin/default.png"));
        else {
            try {
                $response = $downloadHandler->downloadObject($user, 'skinImage', null, null, false);
            } catch (NoFileFoundException $e) { $response = new Response(file_get_contents("images/skin/default.png")); }
        }
        // Set the content type (adjust as needed for your file type)
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

}