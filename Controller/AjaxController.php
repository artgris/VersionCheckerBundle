<?php

namespace Artgris\VersionCheckerBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ajax Controller
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class AjaxController extends Controller
{

    /**
     * @return JsonResponse
     * @Route("/artgris-vcb-ajax", name="artgris_vcb_ajax")
     */
    public function ajaxGetVersionAction()
    {
        $twigRender = $this->get('twig')->render('@ArtgrisVersionChecker/data_collector/toolbar.html.twig', ['versions' => $this->get('version_checker_service')->versionChecker()]);
        return new JsonResponse(['versions' => $twigRender]);

    }

}