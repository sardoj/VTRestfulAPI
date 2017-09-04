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

//Initialization
$dirname = realpath(dirname(__FILE__) . '/../..');
set_include_path($dirname);

chdir($dirname);

include_once 'include/utils/utils.php';
include_once 'includes/Loader.php';
include_once 'includes/http/Request.php';
include_once 'includes/http/Response.php';
include_once 'includes/http/Session.php';
include_once 'includes/runtime/BaseModel.php';
include_once 'includes/runtime/Controller.php';
include_once 'includes/runtime/LanguageHandler.php';
include_once 'includes/runtime/Viewer.php';
include_once 'includes/runtime/Globals.php';

//We include this files else it does not work online
include_once 'modules/RestfulApi/RestfulApi.php';
include_once 'modules/RestfulApi/models/Rest.php';
include_once 'modules/RestfulApi/actions/Api.php';
include_once 'modules/RestfulApi/actions/Auth.php';

$request = new Vtiger_Request($_REQUEST, $_REQUEST);

$api = new RestfulApi_Api_Action();
$api->process($request);