<?php

namespace App\Controller\API;

use App\Controller\BaseController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/api/skin')]
class SkinAPI extends BaseController
{

    private function assetsMap($source_dir, $directory_depth = 0, $hidden = FALSE): array|false
    {
        if ($fp = @opendir($source_dir))
        {
            $filedata   = array();
            $new_depth  = $directory_depth - 1;
            $source_dir = rtrim($source_dir, '/').'/';

            while (FALSE !== ($file = readdir($fp)))
            {
                // Remove '.', '..', and hidden files [optional]
                if ( !trim($file, '.') OR (!$hidden && $file[0] == '.') OR strpos(basename($file), "index.php") !== false)
                {
                    continue;
                }

                if (($directory_depth < 1 OR $new_depth > 0) && @is_dir($source_dir.$file))
                {
                    $filedata[$file] = $this->assetsMap($source_dir.$file.'/', $new_depth, $hidden);
                }
                else
                {
                    $filedata[] = $file;
                }
            }

            closedir($fp);
            ksort($filedata);
            return $filedata;
        }
        return false;
    }

    #[Route('/assets', name: 'skin_assets')]
    public function skin_assets() {
        return $this->json($this->assetsMap("resources"));
    }

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