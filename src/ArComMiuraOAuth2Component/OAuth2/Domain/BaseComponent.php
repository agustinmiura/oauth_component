<?php
/**
* BaseComponent that uses a Silex Container
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2\Domain
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
namespace ArComMiuraOAuth2Component\OAuth2\Domain;

/**
* BaseComponent that uses a Silex Container
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2\Domain
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class BaseComponent
{
    /**
     * @var \Silex\Application 
     */
    protected $app;

    /**
     * Constructor
     * 
     * @param SilexApplication $app [description]
     */
    public function __construct(\Silex\Application  $app)
    {
        if (!self::isValid($app)) {
            $message = ' The constructor called for the class Base component ';
            $message .= ' needs a valid instance of a Silex Application ';
            throw new \RuntimeException($message);
        } 
        $this->app = $app;
    }

    /**
     * Get the Silex Container
     * 
     * @return \Silex\Application Silex application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Validate parameters for constructor
     * 
     * @param  SilexApplication $app Silex application
     * @return boolean               true/false
     */
    public static function isValid(\Silex\Application $app)
    {
        $answer = false;
        if ($app instanceof \Silex\Application) {
            $answer = true;
        }
        return $answer;
    }
}