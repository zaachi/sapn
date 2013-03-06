<?php
/**
 * 
 * @author Jiri Zachar
 * @version 1.0
 * @link http://zaachi.com
 *
 * @about class for sending messages to apple push notification
 */

class SAPN{
	
	//mode
	const DEVELOPMENT = 0;
	const PRODUCTION = 1;

	//developer mode or production mode
	private $mode = 0;
	//sandbox url 
	private $_development_url 	= 'gateway.sandbox.push.apple.com';
	//push original url
	private $_production_url 	= 'gateway.push.apple.com';
	//port
	private $_port = 2195;
	//certificatoin file
	private $_cert_file = null;
	//passphrase code
	private $_passphrase = null;
	//custom variables
	private $_custonVariables = array();
	//badge icon 
	private $_badge = 0;
	//sound 
	private $_sound = 'default';
	//device tokens 
	private $_device_tokens = array();
	//message
	private $_message = null;
	//variable contains the connection identifier
	private $_connection;

	/* exceptions */
	const WRONG_MODE 				= 'environment settings can only be 1 or 0';
	const UNABLE_READ_CERT_FILE 	= 'Unable to read certificate file';
	const NOT_NUMBER 				= 'badge must be integer';
	const NO_VALID_PORT 			= 'Invalid port number';
	const UNABLE_CONNECT			= 'Unable to connect. Url: {%s}, Error:  {%s} - {%s}';
	/* end exceptions */

	/**
	 * 
	 * @param int $mode
	 * @return SAPN
	 */
	public function __construct( $mode = self::DEVELOPMENT )
	{
		if( abs( $mode ) > 1 ){
			throw new Exception( self::WRONG_MODE );
		}
		//return $this;
	}
	
	/**
	 * add new device token into array
	 * @param unknown_type $dev_token
	 * @return SAPN
	 */
	public function addDeviceToken( $dev_token )
	{
		if( !empty($dev_token) ){
			$this->_device_tokens[] = $dev_token;
		}

		return $this;
	}

	/**
	 * Set certificate passphrase
	 * @param string $passphrase
	 * @return SAPN
	 */
	public function setCertificatePassphrase($passphrase)
	{
		$this->_passphrase = $passphrase;
		return $this;
	}

	public function setMessage( $message )
	{
		$this->_message = $message;
	}

	/**
	 * Set certificate file
	 * @param unknown_type $cert_file
	 * @throws Exception
	 * @return SAPN
	 */
	public function setCertificateFile( $cert_file )
	{
		if (!is_readable($cert_file)) {
			throw new Exception( self::UNABLE_READ_CERT_FILE );
		}else{
			$this->_cert_file = $cert_file;
		}
		return $this;
	}

	/**
	 * set the number to badge application icon with
	 * @param int $badge
	 * @throws Exception
	 * @return SAPN
	 */
	public function setBadge($badge)
	{
		if ($badge !== (int)$badge ) {
			throw new Exception( self::NOT_NUMBER );
		}else{
			$this->_badge = $badge;
		}

		return $this;
	}

	/**
	 * Sets the sound that is played when you receive notificatoin
	 * @param string $sound
	 * @return SAPN
	 */
	public function setSound($sound = 'default')
	{
		if( !empty( $sound )){
			$this->_sound = $sound;
		}

		return $this;
	}

	/**
	 * set port to connection
	 * @param int $port
	 * @return SAPN
	 */
	public function setPort($port )
	{
		if ( !is_int( $port &&  $port < 1023 ) ){
			throw new Exception( self::NO_VALID_PORT );
		}else{
			$this->_port = $port;
		}		
		return $this;
	}

	public function setCustomVariable($key, $value)
	{
		if( !empty($key) && !empty($value)){
			$this->_custonVariables[$key] = $value;
		}

		return $this;
	}
	
	public function send()
	{
		if( $this->_connect() === false ){
			$this->_disconnect();
			return;
		}

		$this->_sendData();

		$this->_disconnect();
	}
	
	private function _sendData()
	{
		//get payload
		$payload = $this->_getPayload();
		//get payload lenght
		$payload_length = strlen($payload);

		foreach($this->_getTokens() as $device_token){
			//create apns message
			$apns_message = $this->_packMessage($device_token, $payload, $payload_length);
			//write message
			fwrite($this->_connection, $apns_message);
		}
	}

	private function _packMessage( $device_token, $payload, $payload_length)
	{
		return 	chr(0) . 
				chr(0) . 
				chr(32) . 
				pack('H*', str_replace(' ', '', $device_token)) . 
				chr(0) . 
				chr($payload_length) . 
				$payload;
	}
	
	private function _getTokens()
	{
		$tokens = array_unique($this->_device_tokens);
		return $tokens; 
	}

	private function _getPayload()
	{
		$payload = array();
		$payload['aps'] = array('alert' => $this->_message, 'badge' => intval($this->_badge), 'sound' => $this->_sound);

		if( count( $this->_custonVariables )){
			foreach( $this->_custonVariables as $key=>$value){
				$payload['aps'][$key] = $value;
			}
		}

		$payload = json_encode($payload);
		return $payload;
	}

	private function _connect()
	{
		//create stream context
		$stream_context = stream_context_create();
		
		//set stream context option
		stream_context_set_option($stream_context, 'ssl', 'local_cert',  $this->_cert_file);

		//set passphrase
		if( !empty($this->_passphrase)){
			stream_context_set_option($stream_context, 'ssl', 'passphrase',  $this->_passphrase);
		}

		$error = $error_string = null;
		$this->_connection = stream_socket_client('ssl://' . $this->_getUrl() . ':' . $this->_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);

		if( !$this->_connection){
			throw new Exception(sprintf(self::UNABLE_CONNECT, $this->_getUrl(), $error, $error_string  ));
			return false;
		}
		
		
	}

	private function _disconnect()
	{
		@socket_close($this->_connection);
		@fclose($this->_connection);
	}

	/**
	 * returns the url address as environment settings
	 * @return string
	 */
	private function _getUrl()
	{
		return ($this->mode == self::DEVELOPMENT ? $this->_development_url : $this->_production_url );
	}
}
