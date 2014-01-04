<?php
/**
* Implementation to store the information 
* in a mysql database
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\Storage\MySql
* @package ParametersDao
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
* in a mysql database
* PHP Version 5
*
* @category ArComMiuraOAuth2Component\OAuth2\Storage\MySql
* @package ParametersDao
* @author Miura Agustín <agustin.miura@gmail.com>
* @license  Apache License 2.0
* @link
*/
class ParametersDao
extends \ArComMiuraOAuth2Component\OAuth2\Domain\BaseComponent
{
    /**
     * Get the db connectgor
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDbConnector()
    {
        $app = $this->getApplication();
        $dbConnector = $app['db']; 
        return $dbConnector;
    }

    /**
     * Insert into the parameters table an entity
     *
     * @param array $information Array with the keys
     *                           ['uniqueId'] : UniqueId Strign
     *                           ['name']     : Name String
     *                           ['value']    : String
     * @return  true/false on success
     */
    public function add(array $information)
    {
        if (!self::validForParameterAdd($information)) {
            $message = ' Cannot insert into the parameters table the ';
            $message .= ' information  :'.print_r($information, true);
            throw new \RuntimeException($message);
        }

        $dbConnector = $this->getDbConnector();
        $sql = 'INSERT INTO parameters (uniqueId, name, value)
                VALUES (:uniqueId, :name, :value)
        ';
        $stmt = $dbConnector->prepare($sql);
        $toAdd = array(
            'uniqueId'=>$information['uniqueId'],
            'name'=>$information['name'],
            'value'=>$information['value']
        );
        foreach ($toAdd as $name => $value) {
            $stmt->bindValue($name, $value, null);
        }
        return $stmt->execute();
    }

    
    /**
     * Validate if the array contains valid information
     * to add to the parameters table
     * 
     * @param  array    $information Array with keys:uniqueId,name,valie
     * 
     * @return boolean  Return true/false if it is valid
     */
    public static function validForParameterAdd(array $information)
    {
        $keys = array('uniqueId', 'name', 'value');
        $isValid = true;

        foreach ($keys as $name) {
            if (!isset($information[$name])) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    /**
     * Find a parameter by uniqueId and name
     * @param  String $id       uniqueId
     * @param  String $name     parameter name
     * @return Array/null       If the parameter exist
     *                          then return an array['id']
     *                                         array['uniqueId']
     *                                         array['value']
     *                           Or null if it does not exist
     */
    public function findByIdName($id, $name)
    {
        $dbConnector = $this->getDbConnector();
        $sql = 'SELECT * FROM parameters WHERE uniqueId = :uniqueId 
                    AND name = :name LIMIT 1
        ';
        $stmt = $dbConnector->prepare($sql);
        $stmt->bindValue('uniqueId', $id);
        $stmt->bindValue('name', $name);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $firstRow = null;
        if (count($rows)>=1) {
            $firstRow = array_pop($rows);
        }
        return $firstRow;
    }

    /**
     * Remove by id and name
     * 
     * @param  String $id    Unique id
     * @param  String $name  Parameter name
     * 
     * @return boolean       true/false on success
     */
    public function removeByIdName($id, $name)
    {
        $dbConnector = $this->getDbConnector();
        $sql = '
            DELETE FROM parameters WHERE uniqueId = :uniqueId
                AND name = :name
        ';
        $stmt = $dbConnector->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->bindValue('uniqueId', $id);
        $success = $stmt->execute();

        return $success;
    }

}