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

class RestfulApi {

    protected $crmUrl;
    protected $token;

    public function signInByLoginPassword($crmUrl, $userLogin, $userPassword)
    {
        $result = false;

        if(substr($crmUrl, -1) != '/')
        {
            $crmUrl .= '/';
        }

        $this->crmUrl = $crmUrl;

        $url = $this->crmUrl . 'modules/RestfulApi/index.php?module=Auth&login=' . $userLogin . '&password=' . $userPassword;

        $response = $this->callUrl($url);

        if($response->success)
        {
            $this->token = $response->result;

            $result = $this->token;
        }
        else
        {
            $result = $response;
        }

        return $result;
    }
    
    public function signInByKey($crmUrl, $userAccessKey)
    {
        $result = false;
        
        if(substr($crmUrl, -1) != '/')
        {
            $crmUrl .= '/';
        }

        $this->crmUrl = $crmUrl;

        $url = $this->crmUrl . 'modules/RestfulApi/index.php?module=Auth&key=' . $userAccessKey;

        $response = $this->callUrl($url);

        if($response->success)
        {
            $this->token = $response->result;

            $result = $this->token;
        }
        else
        {
            $result = $response;
        }

        return $result;
    }

    public function setToken($crmUrl, $token)
    {
        if(substr($crmUrl, -1) != '/')
        {
            $crmUrl .= '/';
        }

        $this->crmUrl = $crmUrl;
        $this->token = $token;
    }

    public function call($url_params, $a_params=array())
    {
        if(!preg_match('`&token=`', $url))
        {
            $url_params .= '&token=' . $this->token;
        }

        $crmUrl = $this->crmUrl;

        if(!preg_match('`^' . $crmUrl . '`', $url_params))
        {
            $url = $this->crmUrl . "modules/RestfulApi/index.php?" . $url_params;
        }
        else
        {
            $url = $url_params;
        }

        return $this->callUrl($url, $a_params);
    }

    protected function callUrl($url, $a_params=array())
    {
        //Use Curl
        $ch = curl_init();

        if(!empty($a_params) && $a_params != "DELETE")
        {
            //Sanitize params
            $a_params = $this->sanitizeParams($a_params);

            //url-ify the data for the POST
            $params_string = "";
            foreach ($a_params as $key => $value) {
                $params_string .= $key . '=' . $value . '&';
            }
            rtrim($params_string, '&');


            curl_setopt($ch, CURLOPT_POST, count($a_params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        }

        //If there is ?id= or &id= in the url, set PUT method
        if(preg_match('/[?|&]id=/', $url) && $a_params=="DELETE")
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        elseif(preg_match('/[?|&]id=/', $url) && !empty($a_params))
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        //Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        //Execute request
        $result = curl_exec($ch);
        //echo 'Curl error: ' . curl_error($ch);

        //Close connection
        curl_close($ch);

        $o_result = json_decode($result);

        return $o_result;
    }

    protected function sanitizeParams($a_params)
    {
        if(is_array($a_params))
        {
            foreach ($a_params as $key => $value) {
                $a_params[$key] = str_replace(
                    array(
                        "&#39;",
                        "+",
                        "/"
                    ),
                    array(
                        "%27",
                        "%2B",
                        "\/"
                    ),
                    $value
                );
            }
        }

        return $a_params;
    }
}