<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public License, v.2.0.
 * If a copy of the MPL was not distributed with this file, 
 * you can obtain one at http://mozilla.org/MPL/2.0/.
 * 
 * The Original Code is VTRestfulAPI.
 * 
 * The Initial Developer of the Original Code is Jonathan SARDO.
 * Portions created by Jonathan SARDO are Copyright (C). All Rights Reserved.
 */

require_once('RestfulApi.php');

class RestfulApiCalls {

    protected $api;

    public function __construct()
    {
        $this->api = new RestfulApi();
    }

    public function setToken($crmUrl, $token)
    {
        return $this->api->setToken($crmUrl, $token);
    }
    
    public function signInByLoginPassword($crmUrl, $userLogin, $userPassword)
    {
        return $this->api->signInByLoginPassword($crmUrl, $userLogin, $userPassword);
    }

    public function signInByKey($crmUrl, $userAccessKey)
    {
        return $this->api->signInByKey($crmUrl, $userAccessKey);
    }

    public function getEntities($moduleName, $criteria="", $start=0, $length=20, $order="CE.createdtime DESC")
    {
        $url_params = "module=$moduleName&start=$start&length=$length&order=".urlencode($order)."&criteria=$criteria";
        
        $o_response = $this->api->call($url_params);

        if($o_response->success)
        {
            $a_entities = $o_response->result;
        }
        else
        {
            $a_entities = $o_response;
        }

        return $a_entities;
    }

    public function getEntityById($moduleName, $id)
    {
        $o_entity = null;

        $url_params = "module=$moduleName&id=$id";

        $o_response = $this->api->call($url_params);

        if($o_response->success)
        {
            $o_entity = $o_response->result;
        }
        else
        {
            $o_entity = $o_response;
        }

        return $o_entity;
    }

    public function getPickListValues($moduleName, $pickListName, $getDependencies=false)
    {
        $url_params = "module=$moduleName&picklist=$pickListName";
        
        if($getDependencies)
        {
            $url_params .= '&picklistdep=1';
        }

        $o_response = $this->api->call($url_params);

        if($o_response->success)
        {
            $return = $o_response->result;
        }
        else
        {
            $return = $o_response;
        }

        return $return;
    }

    public function createEntity($moduleName, $a_params)
    {
        $url_params = "module=$moduleName";
        
        $o_response = $this->api->call($url_params, $a_params);

        if($o_response->success)
        {
            $entityId = $o_response->result;
        }
        else
        {
            $entityId = $o_response;
        }

        return $entityId;
    }
    
    public function updateEntity($moduleName, $id, $a_params)
    {
        $url_params = "module=$moduleName&id=$id";
        
        $o_response = $this->api->call($url_params, $a_params);

        if($o_response->success)
        {
            $entityId = $o_response->result;
        }
        else
        {
            $entityId = $o_response;
        }

        return $entityId;
    }

    public function deleteEntity($moduleName, $id)
    {
        $b_deleted = false;

        $url_params = "module=$moduleName&id=$id";
        
        $o_response = $this->api->call($url_params, "DELETE");

        if($o_response->success && $o_response->result != false)
        {
            $b_deleted = true;
        }

        return $b_deleted;
    }
}