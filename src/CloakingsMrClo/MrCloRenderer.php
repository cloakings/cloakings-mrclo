<?php

namespace Cloakings\CloakingsMrClo;

use Cloakings\CloakingsCommon\CloakerResult;
use Cloakings\CloakingsCommon\CloakModeEnum;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MrCloRenderer
{
    public function __construct(
        private readonly string $baseIncludeDir,
    ) {
    }

    public function render(
        CloakerResult $cloakerResult,
        MrCloRenderParams $params,
        Request $request = null,
    ): Response
    {
        $response = new Response();

        if ($cloakerResult->mode === CloakModeEnum::Fake) {
            $mode = $params->fakeMode;
            $target = $params->fakeTarget;
        } else {
            $mode = $params->realMode;
            $target = $params->realTarget;
        }

        if ($mode === MrCloLandingModeEnum::Content) {
            $response->setContent($target);
        } elseif ($mode === MrCloLandingModeEnum::Local) {
            $filename =
                rtrim($this->baseIncludeDir, '\\/') .
                '/' .
                ltrim($target, '\\/');

            try {
                $file = new File($filename);
                $response->setContent($this->include($filename));
                if (!headers_sent()) {
                    $response->headers->set('Content-Type', $file->getMimeType());
                    $response->headers->set('Content-Size', strlen($response->getContent()));
                }
            } catch (\Throwable) {
            }
        } elseif ($mode === MrCloLandingModeEnum::Iframe) {
            $target = htmlspecialchars($target);
            if ($request && $request->query->all()) {
                $target .=
                    (str_contains($target, '?') ? '&' : '?') .
                    http_build_query($request->query->all());
            }

            $template = '<!DOCTYPE html><iframe src="https://{target}" style="width:100%;height:100%;position:absolute;top:0;left:0;z-index:999999;border:none;"></iframe>';
            $content = str_replace('{target}', $target, $template);
            $response->setContent($content);
        } elseif ($mode === MrCloLandingModeEnum::Redirect) {
            $response = new RedirectResponse($target);
        }

        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    private function include(string $filename): string
    {
        ob_start();
        include($filename);

        return ob_get_clean();
    }
}
