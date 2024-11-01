<?php

/**
 * This simple class provides an example PHP interface into the Cirip platform.
 * 
 * Please submit any bug fixes or improvements to cip@cirip.ro with the subject
 * line of API bug fixes.
 * 
 * If you are using this file for a basis to write a connector class for another 
 * language other than php and feel like submitting it for others to use please email
 * your file to cip@cirip.ro with a subject line of API {language} example.
 * 
 * @version 0.1
 * @package Cirip API
 *
 */
class ciripAPIbase
{
	
	/**
	 * Set version number for checking latest version against server version
	 *
	 * @var float
	 */
	private $version = 0.1;
	
	/**
	 * Set request URL
	 *
	 * @var string
	 */
	public $requestURL = "www.cirip.ro";
	
	/**
	 * Set request protocol
	 *
	 * @var unknown_type
	 */
	protected $requestProtocol = "http";
	
	/**
	 * Hold the authentication key
	 *
	 * @var string
	 */
	private $credentials = null;
	
	/**
	 * Provide XML translation table
	 *
	 * @var array
	 */
	public $xmlTranslationTable = array (
										"&" => "&amp;", 
										"<" => "&lt;", 
										">" => "&gt;", 
										"'" => "&#39;", 
										'"' => "&quot;" );
	
	/**
	 * Limits to set
	 *
	 * @var array
	 */
	private $limits = array (
							"max_execution_time" );
	
	/**
	 * Set authentication values
	 *
	 * @param string $apiKey
	 * @param string $apiSecret
	 */
	public function __construct($apiUsername, $apiPassword = "")
	{
		foreach ( $this->limits as $limit )
		{
			ini_set ( $limit, 0 );
		}
		
		$this->credentials = sprintf ( "%s:%s", $apiUsername, $apiPassword );
	}
	
	/**
	 * Reset any changed limits
	 *
	 */
	public function __destruct()
	{
		foreach ( $this->limits as $limit )
		{
			ini_restore ( $limit );
		}
	}
	
	/**
	 * Call server methods
	 *
	 * @param string $method
	 * @param array $params
	 */
	protected function call_method($method, $params = array(), $require_credentials = false)
	{
		$xml = $params;
		
		$this->requestURL = $method;
		$urlInfo = parse_url ( $this->requestProtocol . "://" . $this->requestURL );
		$port = (eregi ( "https|ssl", $urlInfo ["scheme"] )) ? 443 : 80;
		
		// curl
		if (function_exists ( "curl_init" ))
		{
			$ch = curl_init ( $this->requestURL );
			if (! $ch)
			{
				exit ( "CURL: Error connecting to the server: " . curl_errno ( $ch ) . " : " . curl_error ( $ch ) . "<br />" );
			}
			if ($require_credentials)
			{
				curl_setopt ( $ch, CURLOPT_USERPWD, $this->credentials );
			}
			curl_setopt ( $ch, CURLOPT_AUTOREFERER, true );
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt ( $ch, CURLOPT_POST, true );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			if (count ( $params ) > 0)
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
			curl_setopt ( $ch, CURLOPT_USERAGENT, "Cirip API - CURL PHP " . phpversion () );
			curl_setopt ( $ch, CURLOPT_PORT, $port );
			curl_setopt ( $ch, CURLOPT_VERBOSE, true );
			$result = curl_exec ( $ch );
			if (! $result)
			{
				exit ( "CURL: Problem executing request, try changing above set options and re-requesting: " . curl_errno ( $ch ) . " : " . curl_error ( $ch ) . "<br />" );
			}
			curl_close ( $ch );
			return $result;
		} else
		{
			exit ( "The Cirip API object requires the use of cURL extension" );
		}
	}
	
	/**
	 * Encode XML characters if needed
	 *
	 * @param string $str
	 * @return string
	 */
	private function xmlEncode($str)
	{
		return strtr ( $str, $this->xmlTranslationTable );
	}
	
	/**
	 * Decode XML characters if needed
	 *
	 * @param string $str
	 * @return string
	 */
	private function xmlDecode($str)
	{
		return strtr ( $str, array_flip ( $this->xmlTranslationTable ) );
	}
}

/**
 * Cirip API Client
 * 
 * @copyright Timsoft 2008
 *
 */
class ciripAPIclient extends ciripAPIbase
{
	
	/**
	 * Check the version of the API in use
	 *
	 * @return string
	 */
	public function getVersion($format)
	{
		$api_call = sprintf ( "http://www.cirip.ro/statuses/api_version.%s", $format );
		return $this->call_method ( $api_call );
	}
	
	/**
	 * Get public messages 
	 *
	 * @param string $format (format - json or xml)
	 * @param integer $count (how many records to return - none will return 20)
	 * @param integer $since_id (id offset from which to return - none will return from start)	 
	 * @return string
	 */
	public function getPublicTimeline($format, $count = 20, $since_id = 0)
	{
		$api_call = sprintf ( "http://www.cirip.ro/statuses/public_timeline.%s", $format );
		if ($since_id > 0)
		{
			$api_call .= sprintf ( "?since_id=%d", $since_id );
		}
		
		return $this->call_method ( $api_call, array (
														"count" => $count ) );
	}
	
	/**
	 * Add a new status 
	 *
	 * @param string $format (format - json or xml)
	 * @param integer $status (the new message - must have no more than 140 characters)
	 * @return string
	 */
	public function updateStatus($format, $status)
	{
		$api_call = sprintf ( "http://www.cirip.ro/statuses/update.%s", $format );
		
		return $this->call_method ( $api_call, array (
														"status" => $status ), true );
	}
}
?>