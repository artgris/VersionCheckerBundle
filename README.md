# VersionCheckerBundle
Symfony Bundle to get list of installed packages in your project with a comparative of your versions (Parse composer.lock) 
and github versions (GitHub Api v3) . Accessible via Symfony Web Debug Toolbar (using Cache), service and twig extension.


![Image of adding toolbar](http://github.artgris.me/images/versioncheckerbundle.png?v=2)

*Versions available in Symfony Web Debug Toolbar*

Requirements
------------

`module php_curl`


Installation
------------

### 1) Download 

`composer require artgris/version-checker-bundle`

### 2) Enable Bundle

    // app/AppKernel.php
    
    $bundles = array(
        // ...
          new Artgris\VersionCheckerBundle\ArtgrisVersionCheckerBundle()
    );

### 3) Configure the Bundle 


Adds following configurations 

to `app/config/routing_dev.yml`

     _artgris_version_checker:
          resource: "@ArtgrisVersionCheckerBundle/Resources/config/routing.yml"
          
You don't have to have routing restriction on '/artgris-vcb-ajax' 


to ` app/config/config.yml` (optional) :

```yml  
artgris_version_checker:
    access_token: xxxxx...
    lifetime: 3600
``` 
`access_token` : optional but **necessary if you have more than 60 packages** -  It's your token to use GitHub API without rate limit =>  [Generate your token](https://github.com/settings/tokens/new) _(required GitHub user account)_

`lifetime` : Cache lifetime (seconds), GitHub Versions have been saved with `The Cache Component`.  

          
####GitHub Api rate limit


VersionCheckerBundle uses GitHub API v3 to get last releases of your packages. 

But GitHub has Rate Limiting policy :

    For requests using Basic Authentication or OAuth, you can make up to 5,000 requests per hour. 
    For unauthenticated requests, the rate limit allows you to make up to 60 requests per hour.

That's why it's necessary to use a token when you have more than 60 packages.


Usage
=====

#### Symfony Web Debug Toolbar
 
 (screenshot above)

#### Service

    $this->get('version_checker_service')->versionChecker($gitHubName = null)
    
     
exemple :

    $this->get('version_checker_service')->versionChecker() 

return an array with all of your packages : 

    [
        "doctrine/dbal" => [
            "yourVersion" => "v2.5.6"
            "url" => "https://github.com/doctrine/dbal.git"
            "gitHubVersion" => "v2.5.7"
        ],[
        "doctrine/DoctrineBundle" => [
            "yourVersion" => "1.6.6"
            "url" => "https://github.com/doctrine/DoctrineBundle.git"
            "gitHubVersion" => "1.6.6"
        ],[
        ...
        ]      
    ]
     
or get a unique package version :

    $this->get('version_checker_service')->versionChecker('doctrine/dbal')
    
 
#### Twig extension

same logic :

    version_checker()
    
    version_checker(packageName)

#### No release found

If you have message `No release found` for a package, it's because he doesn't have any **published full release**.