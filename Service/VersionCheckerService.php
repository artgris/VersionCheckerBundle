<?php


namespace Artgris\VersionCheckerBundle\Service;


use ComposerLockParser\ComposerInfo;
use ComposerLockParser\Package;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Version checker Service
 * @author Arthur Gribet <a.gribet@gmail.com>
 */
class VersionCheckerService
{
    /**
     * @var KernelInterface
     */
    private $kernel;
    private $accessToken;
    /**
     * @var FilesystemAdapter
     */
    private $cache;


    /**
     * VersionCheckerService constructor.
     * @param KernelInterface $kernel
     * @param $artgrisVersionChecker
     */
    public function __construct(KernelInterface $kernel, $artgrisVersionChecker)
    {
        $this->kernel = $kernel;
        $this->accessToken = $artgrisVersionChecker['access_token'];
        // Save in cache, default lifetime 3600s
        $this->cache = new FilesystemAdapter('versionChecker', $artgrisVersionChecker['lifetime']);
    }

    public function versionChecker($gitHubName = null)
    {
        // Parse composer.lock
        $composerInfo = new ComposerInfo($this->kernel->getRootDir() . '/../composer.lock');
        $composerInfo->parse();
        $packages = $composerInfo->getPackages();

        $packagesList = [];
        foreach ($packages as $package) {
            /** @var Package $package */
            preg_match_all("/https:\/\/github.com\/(.*).git/", $package->getSource()['url'], $matches);
            $gitHubNameTmp = $matches[1][0];
            $packagesList[$gitHubNameTmp] = [
                'yourVersion' => $package->getVersion(),
                'url' => $package->getSource()['url']
            ];
        }
        if ($gitHubName) {
            $packagesList[$gitHubName]['gitHubVersion'] = $this->getOrSetCacheItem($gitHubName);
            return $packagesList[$gitHubName];
        } else {
            foreach ($packagesList as $gitHubNameTmp => $package) {
                $packagesList[$gitHubNameTmp]['gitHubVersion'] = $this->getOrSetCacheItem($gitHubNameTmp);
            }
            return $packagesList;
        }

    }

    /**
     * Get or Save item from cache
     * @param $gitHubName
     * @return mixed
     */
    private function getOrSetCacheItem($gitHubName)
    {
        $responseCache = $this->cache->getItem('github_' . str_replace('/', '_', $gitHubName));
        if (!$responseCache->isHit()) {
            $githubVersion = $this->callGitHubApi($gitHubName);
            if ($githubVersion['save']) {
                $responseCache->set($githubVersion['version']);
                $this->cache->save($responseCache);
            } else {
                return $githubVersion['message'];
            }
        }
        return $responseCache->get();
    }

    /**
     * github last tag from $gitHubName (:owner/:repo)
     * GET /repos/:owner/:repo/releases/latest
     * https://developer.github.com/v3/repos/releases/#get-the-latest-release
     * @param $gitHubName
     * @return array
     */
    private function callGitHubApi($gitHubName)
    {
        $methode = "/releases/latest";
        if ($this->accessToken) {
            $methode .= '?access_token=' . $this->accessToken;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/' . $gitHubName . $methode);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'artgris');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response);

        switch ($httpCode) {
            case '200' :
                return ['save' => true, 'version' => property_exists($data, 'message') ? $data->message : $data->tag_name];
            case '403':
                return ['save' => false, 'message' => $data->message . ' - ' . $data->documentation_url];
            case '404':
                return ['save' => true, 'version' => 'No release found'];
            default:
                return ['save' => false, 'message' => property_exists($data, 'message') ? $data->message : 'error'];
        }
    }

}