<?php
/**
* Abstract class for the information end point getters
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package \OAuth2\Http
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
namespace ArComMiuraOAuth2Component\OAuth2\InformationEndPoint;
/**
* Abstract class for the information end point getters
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package \OAuth2\Http
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
abstract class AbstractGetter
{
    /**
     * OAuth2 config
     * @var Array
     */
    protected $config;

    /**
     * @var \Guzzle\Http\HttpClient 
     */
    protected $client;

    /**
     * @var \Logger $logger
     */
    protected $logger;

    /**
     * Constructor for the AbstractGetter
     * 
     * @param array            $config OAuth2 config
     * @param GuzzleHttpClient $client HttpClient
     * @param Logger           $logger Logger
     */
    public function __construct(array $config
        , \Guzzle\Http\Client $client, \Logger $logger) {
        if (!self::isValid($config, $client, $logger)) {
            $message = 'Invalid parameters for the AbstractGetter constructor';
            $message .= ' invalid config or http client';
            throw new \RuntimeException($message);
        } else {
            $this->config = $config;
            $this->client = $client;
            $this->logger = $logger;
        }
    }

    /**
     * Validate the parameters for the constructor
     * 
     * @param  array            $config OAuth2 config
     * @param  GuzzleHttpClient $client HttpClient
     * @param  Logger           $logger Logger
     * @return boolean                  
     */
    public static function isValid(
        array $config, 
        \Guzzle\Http\Client $client,
        \Logger $logger) {

        $isValid = (is_array($config));
        $isValid = $isValid && ($client instanceof \Guzzle\Http\Client);
        return $isValid && ($logger instanceof \Logger);
    }

    /**
     * Abstract method Get information
     * 
     * @param  array        $informationEndPoints Information end points
     * @param  String       $accessToken          Access token
     * @return \StdClass    Information user
     */
    abstract function getInformation(array $informationEndPoints, $accessToken);

}