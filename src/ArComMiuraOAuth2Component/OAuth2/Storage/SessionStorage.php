<?php
/**
* Implementation to store the information 
* in the web session
* 
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2
* @package Storage
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
namespace ArComMiuraOAuth2Component\OAuth2\Storage;
/**
* Implementation to store the information 
* in the web session
* 
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2
* @package Storage
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*
*/
class SessionStorage 
extends \ArComMiuraOAuth2Component\OAuth2\Domain\BaseComponent
implements \ArComMiuraOAuth2Component\OAuth2\Storage\IStorageHelper 
{
    /**
     * Get session
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface session for symfony 2
     */
    private function _getSession()
    {
        $app = $this->getApplication();
        return $app['session'];
    }

    /**
     * Save the key and value
     * @param  String $key  
     * @param  String $value 
     * @return 
     */
    private function _save($key, $value)
    {
        $session = $this->_getSession();
        $session->set($key, $value);
    }

    /**
     * Get the parameters
     * @param  String $key          
     * @param  String $defaultValue 
     * @return Value or defaultValue
     */
    private function _get($key, $defaultValue = null) 
    {
        $session = $this->_getSession();
        return $session->get($key, $defaultValue);
    }

    /**
     * Save access token
     * @param  String $id          Id associated with the token
     * @param  String $accessToken Access token
     * @return               
     */
    public function saveAccessToken($id, $accessToken) 
    {
        $session = $this->_getSession();
        $this->_save('accessToken', $accessToken);
    }

    /**
     * Get the access token
     * @param  String $id  
     * @return String/null     
     */
    public function getAccessToken($id) 
    {
        $session = $this->_getSession();
        return $session->get('accessToken', null);
    }

    /**
     * Get access code
     * @param  String $id 
     * @return String
     */
    public function getAccessCode($id) 
    {
        return $this->_get('accessCode', null);
    }

    /**
     * Save the access code
     * @param  String $id         
     * @param  String $accessCode 
     * @return boolean            
     */
    public function saveAccessCode($id, $accessCode)
    {
        $this->_save('accessCode', $accessCode);
    }

    /**
     * Save the user information
     * @param  String   $id              info id
     * @param  StdClass $userInformation User information
     * @return void                    
     */
    public function saveUserInformation($id, \StdClass $userInformation)
    {
        $this->_save('_sessionStorageUserInformation', $userInformation);
    }

    /**
     * Get user information
     * @param  String       $id id
     * @return \StdClass    
     */
    public function getUserInformation($id) 
    {
        return $this->_get('_sessionStorageUserInformation', null);
    }

    /**
     * Remove access code
     * @param  String   $id id
     * @return boolean  true/false
     */
    public function removeAccessCode($id) 
    {
        $this->_save('accessCode', null);
    }

    /**
     * Remove access token
     * @param  String $id 
     * @return boolean 
     */
    public function removeAccessToken($id) 
    {
        $this->_save('accessToken', null);
    }

    /**
     * Remove user information
     * @param  String   id
     * @return boolean  true/false
     */
    public function removeUserInformation($id) 
    {
        $this->save('_sessionStorageUserInformation', null);
    }

}