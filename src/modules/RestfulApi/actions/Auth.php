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

class RestFulApi_Auth_Action extends Vtiger_Action_Controller {

	protected $expirationTimeout = 3600; // 1 hour
	
	public function process(Vtiger_Request $request) {
		
	}
	
		
	public function login($username, $password)
	{	


		$user = CRMEntity::getInstance('Users');
		$user->column_fields['user_name'] = $username;		
		
		$isAuthentificated = $user->doLogin($password);
		
		if(!$isAuthentificated)
		{
			return array("success" => false, "error" => array("code" => "USER_NOT_FOUND", "message" => "User not found"));
		}
		
		//Retrieve user id
		$user_id = $user->retrieve_user_id($username);

		//Simulate a login for the user
		$this->loginAsUser($user_id);

		//Generate a token
		return $this->generateToken($user_id);
	}
	
	public function loginByKey($key)
	{
		$db = PearDatabase::getInstance();
		$query = "SELECT id, user_name
				FROM vtiger_users
				WHERE accesskey = ? 
				AND deleted = 0
				LIMIT 1";
		$result = $db->pquery($query, array($key));
		
		$user = $db->fetchByAssoc($result);
		
		if(empty($user))
		{
			return array("success" => false, "error" => array("code" => "KEY_NOT_FOUND", "message" => "Key not found"));
		}

		$user_id = $user["id"];

		//Simulate a login for the user
		$this->loginAsUser($user_id);
				
		//Generate a token
		return $this->generateToken($user_id);
	}

	protected function loginAsUser($user_id)
	{
		global $current_user;

		//On simule que l'utilisateur se connecte
		$user = CRMEntity::getInstance('Users');
		$current_user = $user->retrieveCurrentUserInfoFromFile($user_id);
	}
	
	protected function generateToken($user_id)
	{
		$db = PearDatabase::getInstance();
		
		//Generate a new token
		$token = md5($_SERVER["REMOTE_ADDR"]."-".mktime());
		
		//Save the token
		$focus = new RestfulApi();
		$focus->mode = '';
		$focus->column_fields["token"] = $token;
		$focus->column_fields["assigned_user_id"] = $user_id;
		$focus->column_fields["ip"] = $_SERVER["REMOTE_ADDR"];
		$focus->column_fields["calls"] =  0;
		$focus->column_fields["expiration_date"] = date("Y-m-d H:i:s", mktime()+$this->expirationTimeout);	
		$focus->column_fields["user_id"] = $user_id;		
		$focus->save("RestfulApi");

		$tokenId = $focus->id;
		
		if(is_int($tokenId) && $tokenId > -1)
		{
			return $token;
		}
		
		return false;
	}
	
	public function checkToken($token)
	{
		$ip = $_SERVER["REMOTE_ADDR"];
		$expirationDate = date("Y-m-d H:i:s");
		

		$db = PearDatabase::getInstance();
		$query = "SELECT API.*, API.restfulapiid AS id
				FROM vtiger_restfulapi API
				INNER JOIN vtiger_crmentity CE ON CE.crmid = API.restfulapiid AND CE.deleted = 0
				WHERE API.token = ?
				AND API.expiration_date >= ?
				AND API.ip = ?
				LIMIT 1";
		$result = $db->pquery($query, array($token, $expirationDate, $ip));

		$row = $db->fetchByAssoc($result);
		
		if(!empty($row["id"]))
		{
			$this->loginAsUser($row["user_id"]);

			$focus = new RestfulApi();
			$focus->retrieve_entity_info($row["id"], 'RestfulApi');
			$focus->id = $row["id"];
			
		}

		if(empty($focus))
		{
			return array("success" => false, "error" => array("code" => "INVALID_TOKEN", "message" => "Invalid token"));
		}
				
		//Update token
		$focus->mode = 'edit';	
		$focus->column_fields["calls"] =  $focus->column_fields["calls"] + 1;
		$focus->column_fields["expiration_date"] = date("Y-m-d H:i:s", mktime()+$this->expirationTimeout);
		$focus->save("RestfulApi");
		
		return array("success" => true, "user_id" => $row["user_id"]);
	}
}