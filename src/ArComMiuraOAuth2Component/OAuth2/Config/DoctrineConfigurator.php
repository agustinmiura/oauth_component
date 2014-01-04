<?php
/**
* Doctrine 2 configurator
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2\Config
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*
* Copyright 2013 Miura Agustín <agustin.miura@gmail.com>
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*
*/
namespace ArComMiuraOAuth2Component\OAuth2\Config;
/**
* Doctrine 2 configurator
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2\Config
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/

use Doctrine\ORM\EntityManager as EntityManager;
use Doctrine\ORM\Configuration as Configuration;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineConfigurator
{
    /**
     * Doctrine orm proxy directory
     * @var String
     */
    private $proxyDirectory;

    /**
     * Doctrine entity directory
     * @var String
     */
    private $entityDirectory;

    /**
     * Constructor
     * 
     * @param String $proxyDirectory  
     * @param String $entityDirectory
     */
    public function __construct($proxyDirectory, $entityDirectory)
    {
        if (self::isValid($proxyDirectory, $entityDirectory)) {
            $message = 'Invalid constructor params for Doctrine configurator';
            $message .= ' proxy dir :'.$proxyDirectory.' , entity directory ';
            $message .= ' : '.$entityDirectory;
            throw new \RuntimeException($message);
        }
        $this->proxyDirectory = $proxyDirectory;
        $this->entityDirectory = $entityDirectory;
    }

    /**
     * Validate the constructor
     * 
     * @param  String  $proxyDirectory  
     * @param  String  $entityDirectory 
     * @return boolean                 
     */
    public static function isValid($proxyDirectory, $entityDirectory)
    {
        $isValid = true;
        $params = array($proxyDirectory, $entityDirectory);
        
        $validDirectory;
        foreach ($params as $directory) {
            $validDirectory = is_dir($directory) && is_readable($directory);
            $validDirectory = $validDirectory && is_string($directory);
            if (!$validDirectory) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    /**
     * Create an em for production
     * @param  array    $dbConfig 
     * @return EntityManager          
     */
    public function createEmForProduction($dbConfig)
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', ROOT_PATH.'/myCode/SampleWebApp/Domain/Entities');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxies', $this->proxyDirectory);
        $classLoader->register();


        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir($this->proxyDirectory);
        $config->setProxyNamespace('Proxies');

        $config->setAutoGenerateProxyClasses(false);

        $paths = array(
            ROOT_PATH.'/myCode/SampleWebApp/Domain/Entities'
        );
        $driverImpl = $config->newDefaultAnnotationDriver($paths);
        $config->setMetadataDriverImpl($driverImpl);
        $cache = new \Doctrine\Common\Cache\ApcCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $connectionOptions = array(
            'driver'   => 'pdo_mysql',
            'host'     => $dbConfig['dbUrl'],
            'dbname'   => $dbConfig['dbName'],
            'user'     => $dbConfig['dbUser'],
            'port'     => $dbConfig['dbPort'],
            'password' => $dbConfig['dbPassword'],
            'charset'  => 'UTF8'
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));

        return $em;
    }

    /**
     * Create em for development
     * 
     * @param  array $dbConfig 
     * @return Em           
     */
    public function createEmForDevelopment($dbConfig)
    {
        return $this->createEm($dbConfig);
    }

    /**
     * Create the em 
     * @param  Array            $dbConfig 
     * @return EntityManager              
     */
    public function createEm($dbConfig)
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
        $classLoader->register();

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', ROOT_PATH.'/myCode/SampleWebApp/Domain/Entities');
        $classLoader->register();
        $classLoader = new \Doctrine\Common\ClassLoader('Proxies', $this->proxyDirectory);
        $classLoader->register();


        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir($this->proxyDirectory);
        $config->setProxyNamespace('Proxies');

        $config->setAutoGenerateProxyClasses(true);

        $paths = array(
            ROOT_PATH.'/myCode/SampleWebApp/Domain/Entities'
        );
        $driverImpl = $config->newDefaultAnnotationDriver($paths);
        $config->setMetadataDriverImpl($driverImpl);
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        $logQueries = (isset($dbConfig['doctrine.debug.sql']))
        ? ($dbConfig['doctrine.debug.sql']==1) : false;
        if ($logQueries) {
            $config->setSQLLogger(new \SampleWebApp\Helper\SqlLogger());
        }

        $connectionOptions = array(
            'driver'   => 'pdo_mysql',
            'host'     => $dbConfig['dbUrl'],
            'dbname'   => $dbConfig['dbName'],
            'user'     => $dbConfig['dbUser'],
            'port'     => $dbConfig['dbPort'],
            'password' => $dbConfig['dbPassword'],
            'charset'  => 'UTF8'
        );

        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));

        return $em;
    }

    /**
     * Createm the helper set
     * 
     * @param  Silex\Application $app 
     * @return HelperSet     
     */
    public function createHelperSet($app) {
        $em = $this->createEm($app);
        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
             'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
             'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
        ));
        return $helperSet;
    }
}