<?php


namespace Artgris\VersionCheckerBundle\Twig;


use Artgris\VersionCheckerBundle\Service\VersionCheckerService;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Twig extension to get package(s) versions
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class VersionCheckerExtension extends \Twig_Extension
{
	/**
	 * @var VersionCheckerService
	 */
	private $checkerService;


    /**
     * VersionCheckerService constructor.
     * @param VersionCheckerService $checkerService
     */
	public function __construct(VersionCheckerService $checkerService)
	{
		$this->checkerService = $checkerService;
	}

	public function versionChecker($gitHubName = null)
	{

		return $this->checkerService->versionChecker($gitHubName);

	}

	public function getFunctions()
	{
		return [
			'version_checker' => new \Twig_SimpleFunction('version_checker', [$this, 'versionChecker']),
		];


	}


}