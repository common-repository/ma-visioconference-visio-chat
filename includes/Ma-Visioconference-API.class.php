<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Ma_Visioconference_API{
  private $apiToken = '';

  // Connect thru https protocol
  var $secure = false;

  // Constructor function
  public function __construct($apiToken=false)
  {
    if ($apiToken)
      $this->apiToken = $apiToken;
    $this->apiUrl = (($this->secure) ? 'https' : 'http') . '://ma-visioconference.diva-cloud.com/query.php';
  }

  public function requestUrlBuilder($method, $params=array(), $request)
  {
    $query_string = array('act' => 'act='.$method,
			  'apitoken' => 'apitoken='.$this->apiToken);
    foreach ($params as $key => $value) {
      if ($request == 'GET' || in_array($key, array('apikey', 'output')))
	$query_string[$key] = $key . '=' . urlencode($value);
    }
    $this->call_url = $this->apiUrl . '?' . join('&', $query_string);
    return $this->call_url;
  }

  public function sendRequest($method = false, $params = array(), $request = 'GET')
  {
    // Method
    $this->_method = $method;
    $this->_request = $request;

    // Build request URL
    $url = $this->requestUrlBuilder($method, $params, $request);

    if (!in_array('curl', get_loaded_extensions()))
      die('Error: You must have cURL extension enabled !');

    // Set up and execute the curl process
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl_handle, CURLOPT_USERPWD, $this->apiKey . ':' . $this->secretKey);
    curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
    curl_setopt($curl_handle, CURLINFO_HEADER_OUT, true);

    $this->_request_post = false;
    if ($request == 'POST')
      {
	curl_setopt($curl_handle, CURLOPT_POST, count($params));
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($params));
	$this->_request_post = $params;
      }

    $buffer = curl_exec($curl_handle);

    // Response code
    $this->_response_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

    // Close curl process
    curl_close($curl_handle);

    // RESPONSE
    $this->_response = ($this->output == 'json') ? json_decode($buffer) : $buffer;

    return ($this->_response_code == 200) ? true : false;
  }

  function get_last_meeting_public(){
    $this->sendRequest('lastMeet', array('type'=>'public'));
    return $this->_response;
  }

  function get_user_details(){
    $this->sendRequest('userDetails', array());
    if ($this->_response != ''){
      $res = explode(' ', $this->_response);
      if (count($res) > 1)
	return $res;
    }
    return array('-', '-');
  }

}

