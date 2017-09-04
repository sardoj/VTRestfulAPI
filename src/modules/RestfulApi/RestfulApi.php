<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class RestfulApi extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_restfulapi';
	var $table_index= 'restfulapiid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_restfulapicf', 'restfulapiid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_restfulapi', 'vtiger_restfulapicf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_restfulapi' => 'restfulapiid',
		'vtiger_restfulapicf'=>'restfulapiid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		'LBL_TOKEN' => array('restfulapi', 'token'),
		'LBL_IP' => array('restfulapi', 'ip'),
		'LBL_EXPIRATION_DATE' => array('restfulapi', 'expiration_date'),
		'LBL_CALLS' => array('restfulapi', 'calls'),

	);
	var $list_fields_name = Array (
		'LBL_TOKEN' => 'token',
		'LBL_IP' => 'ip',
		'LBL_EXPIRATION_DATE' => 'expiration_date',
		'LBL_CALLS' => 'calls',

	);

	// Make the field link to detail view
	var $list_link_field = 'token';

	// For Popup listview and UI type support
	var $search_fields = Array(
		'LBL_CALLS' => array('restfulapi', 'calls'),
		'LBL_EXPIRATION_DATE' => array('restfulapi', 'expiration_date'),
		'LBL_IP' => array('restfulapi', 'ip'),
		'LBL_TOKEN' => array('restfulapi', 'token'),

	);
	var $search_fields_name = Array (
		'LBL_CALLS' => 'calls',
		'LBL_EXPIRATION_DATE' => 'expiration_date',
		'LBL_IP' => 'ip',
		'LBL_TOKEN' => 'token',

	);

	// For Popup window record selection
	var $popup_fields = Array ('token');

	// For Alphabetical search
	var $def_basicsearch_col = 'token';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'token';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('token','assigned_user_id');

	var $default_order_by = 'token';
	var $default_sort_order='ASC';

	function RestfulApi() {
		$this->log =LoggerManager::getLogger('RestfulApi');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('RestfulApi');
	}

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
 		if($eventType == 'module.postinstall') {
 			//Enable ModTracker for the module
 			static::enableModTracker($moduleName);
			//Create Related Lists
			static::createRelatedLists();
		} else if($eventType == 'module.disabled') {
			// Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			// Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			//Create Related Lists
			static::createRelatedLists();
		}
 	}
	
	/**
	 * Enable ModTracker for the module
	 */
	public static function enableModTracker($moduleName)
	{
		include_once 'vtlib/Vtiger/Module.php';
		include_once 'modules/ModTracker/ModTracker.php';
			
		//Enable ModTracker for the module
		$moduleInstance = Vtiger_Module::getInstance($moduleName);
		ModTracker::enableTrackingForModule($moduleInstance->getId());
	}
	
	protected static function createRelatedLists()
	{
		include_once('vtlib/Vtiger/Module.php');	

	}
}