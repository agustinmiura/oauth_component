<?php
/**
* User agent enum for the http clients
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
namespace ArComMiuraOAuth2Component\OAuth2\Http;
/**
* User agent enum for the http clients
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package \OAuth2\Http
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class UserAgentEnum
{
    /**
     * User agent list
     * @var array
     */
    private $agentList;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->agentList = $this->_build();
    }

    /**
     * Build list of user agents
     * 
     * @return array
     */
    private function _build()
    {
        return array(
            'GOOGLE_CHROME_VER_30'=>'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36',
            'GOOGLE_CHROME_VER_29'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.62 Safari/537.36',
            'GOOGLE_CHROME_VER_28'=>'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.2 Safari/537.36',
            'FIREFOX_VER_25'=>'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
            'FIREFOX_VER_24'=>'Mozilla/5.0 (Windows NT 6.0; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0',
            'INTERNET_EXPLORER_10'=>'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)'
        );
    }

    /**
     * With a key get an user agent
     * to use
     * 
     * @param  String $key User agent key
     * @return String
     */
    public function getUserAgentFor($key)
    {   
        $agentList = $this->agentList;
        if (!isset($agentList[$key])) {
            $message = 'Invalid key for getting a user agent from';
            $message .= ' user agent enum with key :'.$key;
            throw new \RuntimeException($message);
        } else {
            return $agentList[$key];
        }
    }

}