<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class NoCacheResponseListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (str_contains($request->getPathInfo(), "/api/")) {
            $response->headers->addCacheControlDirective('no-cache');
            $response->headers->addCacheControlDirective('private');
            $response->headers->addCacheControlDirective('max-age', 0);
            $response->headers->addCacheControlDirective('must-revalidate');
        }
    }
}