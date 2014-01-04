<?php
/**
* OAuth2 Client to interact with a OAuth2 provider
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2
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
namespace ArComMiuraOAuth2Component\OAuth2;

/**
* OAuth2 Client to interact with a OAuth2 provider
* 
* The configuration array that must be supplied with the constructor must
* be like this 
*
*  The array must have the following keys:
* 
*       oauth2['authorizationEndPoint']='https://accounts.google.com/o/oauth2/auth'
*       Then authorization endpoint to request the access token
*        
*       oauth2['response_type']='code'
*       Response used for the OAuth2 authentication
* 
*       oauth2['clientId']='aaa'
*       OAuth2 client id 
* 
*       oauth2['redirect_uri']='http://www.oauth2.example2.com/oauth2callback'
*       OAuth2 uri to send the access code to request the access token
* 
*       oauth2['scope']='https://www.googleapis.com/auth/userinfo.email'
*       Scope used for the OAuth2 request
* 
*       oauth2['state']='ssss'
*       
*       oauth2['access_type']='online'
*       Can be 'online' or 'offline' if you want a refresh token to get new 
*       access tokens
* 
*       oauth2['approval_prompt']='force'
*       Can be 'force'/'auto' . 'force':Each time ask the permission to the user
*       to access the resources . 'auto' : Ask only in the first time
* 
*       oauth2['login_hint']=''
*       
*       oauth2['client_secret']='aaaaa'
*       Client secret to request information
*       
*       oauth2['tokenEndPoint']='https://accounts.google.com/o/oauth2/token'
*       Endpoint to request the user information with the access token
*       
*       oauth2['httpClientLogPath']='/tmp/oauth2/logs/httpClient.log'
*       Absolute path where the log is written to see the http requests in detail
* 
*       oauth2['httpClientInfoRetrieverPath']='/home/user/tmp/oauth2/logs/httpClient.log'
*       Absolute path where the log is writter to see the results of the requests to 
*       get the information
* 
*       oauth2['grantType']='authorization_code'
*       OAuth2 grant types 
* 
*       oauth2['revokeEndPoint']='https://accounts.google.com/o/oauth2/revoke?token=%s'
*       Full path where the %s is going to be replaced with the access token value
*       This url is used to revoke the endpoint
*
* PHP Version 5
*
* @category ArComMiuraOAuth2Component
* @package OAuth2
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class OAuth2Client
{
    /**
     * Array with the configuration parameters
     *
     * 
     * @var Array with the OAuth2 config values
     * 
     */
    protected $config;

    /**
     * Guzzle Http Client to make http requests
     * 
     * @var \Guzzle\Http\Client
     */
    protected $httpClient;

    /**
     * Implementation of Abstract Getter to get information
     * from the OAuth2 provider
     * 
     * @var ArComMiuraOAuth2Component\OAuth2\InformationEndPoint\AbstractGetter
     */
    protected $informationGetter;

    /**
     * User agent enum to get the user agents to make http requests
     *      
     * @var ArComMiuraOAuth2Component\OAuth2\Http\UserAgentEnum User agent enum
     */
    protected $userAgentEnum;

    /**
     * Storage Helper is the class whose responsability
     * is to store the accessCode, accessToken and user information
     * 
     * @var ArComMiuraOAuth2Component\OAuth2\Storage\StorageHelper
     */
    protected $storageHelper;

    /**
     *  Get the user information from the endpoint with this class
     * 
     * @param  array        $informationEndPoints Array with information endpoints
     * @param  String       $accessToken          Access token
     * @return \StdClass    Information of the user
     */
    public function getInformation(array $informationEndPoints, $accessToken)
    {
        return $this->informationGetter->getInformation(
            $informationEndPoints, $accessToken);
    }

    /**
     * Save the access code after the user allowed the access
     * 
     * @param  String $id         Id associated with the data , For eg the sessionid
     * @param  String $accessCode The access code to request the access token
     * @return void              
     */
    public function saveAccessCode($id, $accessCode) 
    {
        $storageHelper = $this->storageHelper;
        $storageHelper->saveAccessCode($id, $accessCode);
    }

    /**
     * Return the access code stored associated
     * with the id
     *
     * @param  String       $id   id associated
     * @return String/null The access code
     */
    public function getAccessCode($id) 
    {
        $storageHelper = $this->storageHelper;
        return ($storageHelper->getAccessCode($id));
    }


    /**
     * Validate the parameters for the constructor .
     * Returns true/false
     * 
     * @param  array                                                            $config            With parameters
     * @param  ArComMiuraOAuth2ComponentOAuth2HttpUserAgentEnum                 $userAgentEnum     UserAgentEnum
     * @param  ArComMiuraOAuth2ComponentOAuth2InformationEndPointAbstractGetter $informationGetter Information getter
     * @return boolean                                                                             true/false
     */
    public static function isValid(array $config
        , \ArComMiuraOAuth2Component\OAuth2\Http\UserAgentEnum $userAgentEnum
        , \ArComMiuraOAuth2Component\OAuth2\InformationEndPoint\AbstractGetter $informationGetter
        , \ArComMiuraOAuth2Component\OAuth2\Storage\IStorageHelper $storageHelper) {

        $answer = is_array($config);
        return $answer 
            && ($userAgentEnum instanceof \ArComMiuraOAuth2Component\OAuth2\Http\UserAgentEnum)
            && ($informationGetter instanceof \ArComMiuraOAuth2Component\OAuth2\InformationEndPoint\AbstractGetter)
            && ($storageHelper instanceof \ArComMiuraOAuth2Component\OAuth2\Storage\IStorageHelper);
    }

    /**
     * Constructor for the OAuth2 Client 
     * 
     * @param array                                                            $config            OAuth2 config object
     * @param ArComMiuraOAuth2ComponentOAuth2HttpUserAgentEnum                 $userAgentEnum     UserAgentEnum
     * @param ArComMiuraOAuth2ComponentOAuth2InformationEndPointAbstractGetter $informationGetter Getter of the object
     */
    public function __construct(array $config
        , \ArComMiuraOAuth2Component\OAuth2\Http\UserAgentEnum $userAgentEnum
        , \ArComMiuraOAuth2Component\OAuth2\InformationEndPoint\AbstractGetter $informationGetter
        , \ArComMiuraOAuth2Component\OAuth2\Storage\IStorageHelper $storageHelper) {
        
        if (!self::isValid($config, 
            $userAgentEnum, $informationGetter, $storageHelper)) {
            
            $message = 'Invalid parameters for the OAuth2 Client , the config';
            $message .= ' is not an array or the userAgentEnum is an invalid ';
            $message .=' is not an object ';

            throw new \RuntimeException($message);
        }

        $this->config = $config;
        $this->userAgentEnum = $userAgentEnum;
        $this->httpClient = $this->_createHttpClient();
        $this->informationGetter = $informationGetter;
        $this->storageHelper = $storageHelper;
    }

    /**
     * Returns the config array used for the client
     * 
     * @return array OAuth2 config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Given an antiforgery state token we want 
     * to generate an authorization end point
     * 
     * @param  String $state Anti forgery token
     * @return String Authorization endpoint
     */
    public function generateAuthorizationEndPoint($state)
    {
        $config = $this->config;
    
        $urlEndPoint = $config['authorizationEndPoint'];
        $responseType = $config['response_type'];
        $clientId = $config['clientId'];
        $redirect_uri = $config['redirect_uri'];
        $scope = $config['scope'];
        /*
        $state = $config['state'];
        */
        $access_type = $config['access_type'];
        $approval_prompt = $config['approval_prompt'];
        $login_hint = $config['login_hint'];

        $url = $urlEndPoint.'?response_type=%s';
        $url .= '&client_id=%s';
        $url .= '&redirect_uri=%s';
        $url .= '&scope=%s';
        $url .= '&state=%s';
        $url .= '&access_type=%s';
        $url .= '&approval_prompt=%s';
        $url .= '&login_hint=%s';

        $url = sprintf($url, $responseType, $clientId, $redirect_uri, $scope
            , $state, $access_type, $approval_prompt, $login_hint);

        return $url;
    }

    /**
     * Get headers for the request to request the 
     * access token . See the OAuth2 implementation
     * 
     * @return array('Accept: application/json,'Content-Type: application/x-www-form-urlencoded')
     */
    private function getHeadersForTokenRequest()
    {   
        return $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );
    }

    /**
     * Revoke the access token sending it 
     * to the token revoke end point
     * 
     * @param  String $accessToken Access token
     * @return Decoded answer it can be a \StdClass
     *         if it is {"error":"invalid token"}  => Then there is an error
     *         if it is {} => Then is valid        
     */
    public function revokeToken($accessToken) 
    {
        $config = $this->config;
        $httpClient = $this->httpClient;

        $url = $config['revokeEndPoint'];
        $url = sprintf($url, $accessToken);

        $rawAnswer = $httpClient->get($url)->send();
        $answer = json_decode($rawAnswer->getBody());

        if (isset($answer->error)) {
            $message = 'The access token %s cannot be revoked because is not';
            $message .= ' valid for the OAuth2 provider ';
            throw new \RuntimeException($message);
        }

        return $answer;
    }

    /**
     * With the authorization code request the 
     * user information to the information endpoints
     * 
     * @param  string       $code auhtorization code
     * @return \StdClass    StdClass object with attributes
     */
    public function requestAccessToken($code)
    {
        $config = $this->config;
        $httpClient = $this->httpClient;

        $tokenEndPoint = $config['tokenEndPoint'];
        $headers = $this->getHeadersForTokenRequest();

        $code = $code;
        $clientId = $config['clientId'];
        $clientSecret = $config['client_secret'];
        $redirectUri = $config['redirect_uri'];
        $grantType = $config['grantType'];
        $params = array(
            'code'=>$code,
            'client_id'=>$clientId,
            'client_secret'=>$clientSecret,
            'redirect_uri'=>$redirectUri,
            'grant_type'=>$grantType
        );

        $method = 'POST';
        $url = $tokenEndPoint;
        $request = new \Guzzle\Http\Message\EntityEnclosingRequest($method, 
            $url, $headers);
        $request->addPostFields($params);
        $response = $httpClient->send($request);

        /**
         * Parse the answer here
         */
        $code = $response->getStatusCode();
        $codeMessage = $response->getReasonPhrase();
        $isError = $response->isError();
        $body = $response->getBody(true);
        $isSuccessfull = $response->isSuccessful();

        return array(
            'code'=>$code,
            'codeMessage'=>$codeMessage,
            'isError'=>$isError,
            'isSuccessfull'=>$isSuccessfull,
            'body'=>$body
        );
    }

    /**
     * Get the logger for the token request
     * 
     * @return \Logger Log4php Logger
     */
    public function getLoggerForTokenRequest()
    {
        return (\Logger::getLogger('MyLogger'));
    }

    /**
     * Get the logger for the token persist
     * 
     * @return \Logger Log4php Logger
     */
    public function getLoggerForTokenPersist()
    {
        return \Logger::getLogger('MyLogger');
    }

    /**
     * Get the logger for the information getter
     * 
     * @return \Logger Log4php Logger
     */
    public function getLoggerForInformationGetter()
    {
        return (\Logger::getLogger('MyLogger'));
    }

    /**
     * Persist the token information with the client
     *
     * @param  String   $accessCode     Access code
     * @param  StdClass $tokenResponse  StdClass instance with properties
     * @return void                  
     *      
     */
    public function persistTokenInformation(
        $accessCode, \StdClass $tokenResponse) {

        $logger = \Logger::getLogger('MyLogger');
        
        $asString = print_r($tokenResponse, true);
        $message = ' Persist the token information , acccessCode , tokenResponse';
        $message .= ' accessCode : %s, tokenResponse :%s';
        $message = sprintf($message, $accessCode, $asString);

        $logger->debug($message);
    }

    /**
     * Create a httpClient to query the endpoints .
     * 
     * @param  String $logPath      Path of the file to log the requests . For example : "/tmp/log.log"
     * @return \Guzzle\Http\Client  Guzzle http client
     */
    public static function createHttpClient($logPath) 
    {
        $httpClient = new \Guzzle\Http\Client();

        $monologLogger = new \Monolog\Logger('oauth2-logger');
        $monologLevel = \Monolog\Logger::DEBUG;
        $streamHandler = new \Monolog\Handler\StreamHandler($logPath);
        $monologLogger->pushHandler($streamHandler);
        
        $monologAdapter = new \Guzzle\Log\MonologLogAdapter($monologLogger);
        $adapterFormat = \Guzzle\Log\MessageFormatter::DEBUG_FORMAT;
        $monologPlugin = new \Guzzle\Plugin\Log\LogPlugin($monologAdapter, $adapterFormat);
        $httpClient->addSubscriber($monologPlugin);

        //
        $httpClient->setUserAgent(
            'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36'
        );
        //set ssl options
        $httpClient->setSslVerification(false, false, 2);

        return $httpClient;
    }

    /**
     * Create the httpClient to make the http request to the api
     * 
     * @return \HttpClient to make the request
     */
    protected function _createHttpClient()
    {
        $config = $this->config;
        $path = $config['httpClientLogPath'];
        $httpClient = self::createHttpClient($path);
        
        $userAgent = $this->userAgentEnum->getUserAgentFor(
            'GOOGLE_CHROME_VER_30'
        );
        
        $httpClient->setUserAgent($userAgent);
        //set ssl options
        $httpClient->setSslVerification(false, false, 2);

        return $httpClient;
    }

    /**
     * Save the access token with the client
     * 
     * @param  String $id          Id associated with the data
     * @param  String $accessToken Access token to request information
     * @return void
     */
    public function saveAccessToken($id, $accessToken)
    {
        $storageHelper = $this->storageHelper;
        $storageHelper->saveAccessToken($id, $accessToken);
    }

    /**
     * Get the access token given an id
     * 
     * @param  String $id  Id associated with the data
     * @return String access token
     */
    public function getAccessToken($id) 
    {
        $storageHelper = $this->storageHelper;
        return ($storageHelper->getAccessToken($id));
    }

    /**
     * Save the user information associated with the id
     * 
     * @param  String   $id              Id associated with the data
     * @param  StdClass $userInformation Std class with the information
     * @return boolean true/false
     */
    public function saveUserInformation($id, \StdClass $userInformation)
    {
        $storageHelper = $this->storageHelper;
        $storageHelper->saveUserInformation($id, $userInformation);
    }

    /**
     * Get the user information
     * @param  String       $id Id associated with the data
     * @return \StdClass    User information 
     */
    public function getUserInformation($id)
    {
        $storageHelper = $this->storageHelper;
        return ($storageHelper->getUserInformation($id));
    }

    /**
     * Remove the access code from the storage
     * 
     * @param  String id associatted with the data  
     * @return boolean 
     */
    public function removeAccessCode($id)
    {
        return $this->storageHelper->removeAccessCode($id);
    }

    /**
     * Remove the access token
     * 
     * @param  String $id  Id associated with the data
     * @return void    
     */
    public function removeAccessToken($id) 
    {
        return $this->storageHelper->removeAccessToken($id);
    }

    /**
     * Remove the user information  associated with the data
     * 
     * @param  String $id  Id associated with the data
     * @return boolean true/false
     */
    public function removeUserInformation($id)
    {
        return $this->storageHelper->removeUserInformation($id);
    }

}
