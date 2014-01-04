<?php
/**
* Implementation to store the information 
* in the web session
* 
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\Storage
* @package MySql
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
namespace ArComMiuraOAuth2Component\OAuth2\Storage\MySql;
/**
* Implementation to store the information 
* in the web session
* 
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\Storage
* @package MySql
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class MySqlStorage
extends \ArComMiuraOAuth2Component\OAuth2\Domain\BaseComponent
implements \ArComMiuraOAuth2Component\OAuth2\Storage\IStorageHelper
{
    const PARAM_NAME_ACCESS_TOKEN = 'accessToken';
    const PARAM_NAME_USER_INFORMATION = 'userInformation';
    const PARAM_NAME_ACCESS_CODE = 'accessCode';

    /**
     * Parameters Dao
     * @var \ArComMiuraOAuth2Component\OAuth2\Storage\MySql\ParametersDao
     */
    protected $parameterDao;

    /**
     * Constructor used 
     * @param ArComMiuraOAuth2ComponentOAuth2StorageMySqlParametersDao $parametersDao Parameters Dao
     */
    public function __construct(
        \ArComMiuraOAuth2Component\OAuth2\Storage\MySql\ParametersDao $parametersDao) {

        if (!self::validate($parametersDao)) {
            $message = ' The constructor for the mysql storage has been called';
            $message .= ' with an invalid parameters dao ';

            throw new \RuntimeException($message);
        }

        $this->parametersDao = $parametersDao;
    }

    /**
     * Validate the parameters
     * @param  ArComMiuraOAuth2ComponentOAuth2StorageMySqlParametersDao $parametersDao Dao
     * @return boolean                                                                 
     */
    public static function validate(\ArComMiuraOAuth2Component\OAuth2\Storage\MySql\ParametersDao $parametersDao)
    {
        return ($parametersDao 
            instanceof \ArComMiuraOAuth2Component\OAuth2\Storage\MySql\ParametersDao);
    }

    /**
     * Save the access token in the db
     * @param  String  $id          Id
     * @param  String  $accessToken Access token
     * @return Boolean              True/false on success
     */
    public function saveAccessToken($id, $accessToken) 
    {
        $toAdd = array(
            'uniqueId'=>$id,
            'name'=>self::PARAM_NAME_ACCESS_TOKEN,
            'value'=>$accessToken
        );
        return $this->parametersDao->add($toAdd);
    }

    /**
     * Get the access token
     * @param  String $id  
     * @return String/null     
     */
    public function getAccessToken($id) 
    {
        return $this->parametersDao->findByIdName($id
            , self::PARAM_NAME_ACCESS_TOKEN);
    }   
    /**
     * Get access code
     * @param  String $id 
     * @return String
     */
    public function getAccessCode($id) 
    {
        return $this->parametersDao->findByIdName($id, 
            self::PARAM_NAME_ACCESS_CODE);
    } 
    /**
    * Save the access code
    * @param  String $id         
    * @param  String $accessCode 
    * @return boolean            
    */
    public function saveAccessCode($id, $accessCode)
    {
        $toAdd = array(
            'uniqueId'=>$id,
            'name'=>self::PARAM_NAME_ACCESS_TOKEN,
            'value'=>$accessCode
        );

        return $this->parametersDao->add($toAdd);
    }   
    /**
     * Save the user information
     * @param  String   $id              info id
     * @param  StdClass $userInformation User information
     * @return void                    
     */
    public function saveUserInformation($id, \StdClass $userInformation)
    {
        $jsonEncoded = json_encode($userInformation);
        $toAdd = array(
            'uniqueId'=>$id,
            'name'=>self::PARAM_NAME_USER_INFORMATION,
            'value'=>$jsonEncoded
        );
        return $this->parametersDao->add($toAdd);
    }
    /**
     * Get user information
     * @param  String       $id id
     * @return \StdClass    
     */
    public function getUserInformation($id) 
    {
        $found = $this->parametersDao->findByIdName($id, self::PARAM_NAME_USER_INFORMATION);
        
        $answer = null;
        if (is_array($found)) {
            $decoded = json_decode($found['value']);
            $answer = array(
                'id'=>$found['id'],
                'uniqueId'=>$found['uniqueId'],
                'name'=>$found['name'],
                'value'=>$decoded
            );
        }
        return $answer;
    }

    /**
     * Remove access code
     * @param  String   $id id
     * @return boolean  true/false
     */
    public function removeAccessCode($id) 
    {
        return $this->parametersDao->removeByIdName($id, 'accessCode');
        //$this->_save('accessCode', null);
        
    }
    /**
     * Remove access token
     * @param  String $id 
     * @return boolean 
     */
    public function removeAccessToken($id) 
    {
        return $this->parametersDao->removeByIdName($id, 'accessToken');
        //$this->_save('accessToken', null);
    }
    /**
     * Remove user information
     * @param  String   id
     * @return boolean  true/false
     */
    public function removeUserInformation($id) 
    {
        return $this->parametersDao->removeByIdName($id, 'userInformation');
    }

}