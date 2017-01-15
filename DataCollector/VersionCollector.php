<?php

namespace Artgris\VersionCheckerBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class VersionCollector extends DataCollector
{

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // TODO: Implement collect() method.
    }


    public function getName()
    {
        return 'app.version_collector';
    }

}