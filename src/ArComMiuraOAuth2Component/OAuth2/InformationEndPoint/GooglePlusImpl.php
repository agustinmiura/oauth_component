<?php
/**
* Implementation to get the information
* from Google
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\
* @package InformationEndpoint
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
* Implementation to get the information
* from Google
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\
* @package InformationEndpoint
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class GooglePlusImpl extends 
\ArComMiuraOAuth2Component\OAuth2\InformationEndPoint\AbstractGetter {
    /**
     * With the access token request the user information
     *
     * From 'v1' url gets  
     * {
     *    "kind": "plus#person",
     *    "etag": "\"r6E4NfYOn5dpL2w8XGt_3gHVskk/XC-YVxgzmihI7tsQQ2JcClbPzqc\"",
     *    "gender": "male",
     *    "objectType": "person",
     *    "id": "106965367834259231263",
     *    "displayName": " ",
     *    "name": {
     *     "familyName": " ",
     *     "givenName": " "
     *    },
     *    "url": "https://plus.google.com/111111",
     *    "image": {
     *     "url": "Url to the image"
     *    },
     *    "isPlusUser": true/false,
     *    "language": "en",
     *    "ageRange": {
     *     "min": 12
     *    },
     *    "verified": false
     * }
     *
     * From v2
     * ["id"]=userid
     * ["email"]=User email .
     * 
     * @param  array  $informationEndPoints 
     *                $informationEndPoints['v1'] = https://www.googleapis.com/oauth2/v2/userinfo?access_token=%s
     *                $informationEndPoints['v2'] = https://www.googleapis.com/plus/v1/people/me?access_token=%s
     * 
     * @param  String       $accessToken  Access token
     * @return \StdClass   \StdClass      object with attributes
     */
    public function getInformation(array $informationEndPoints, $accessToken)
    {
        $logger = $this->logger;

        $v1Url = $informationEndPoints['v1'];
        $v2Url = $informationEndPoints['v2'];

        $httpClient = $this->client;

        $v1Url = sprintf($v1Url, $accessToken);
        $v2Url = sprintf($v2Url, $accessToken);

        /**
        * Get user info
        */
        $url = $v2Url;
        $url = sprintf($url, $accessToken);
        $rawUserInfo = $httpClient->get($url)->send();
        $idInformation = json_decode($rawUserInfo->getBody());
        $asString = print_r($idInformation, true);
        $logger->debug('getInformation User information :'.$asString);

        /**
         * Get profile info
         */
        $url = $v1Url;
        $url = sprintf($url, $accessToken);
        $rawProfileInfo = $httpClient->get($url)->send();
        $profileInformation = json_decode($rawProfileInfo->getBody());
        $asString = print_r($profileInformation, true);
        $logger->debug('getInformation Profile information :'.$asString);

        if (!isset($profileInformation->id)) {
            $message = 'Cannot get profile or user information from ';
            $message .= ' Google OAuth2 information endpoints ';
            $message .= ' and token is '.$accessToken;

            throw new \RuntimeException($message);
        }

        $idInformation->email = $profileInformation->email;

        return $idInformation;
    }
}