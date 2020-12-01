<?php
namespace Fidelis\Driver;

use Fidelis\Exception\Exception;

/**
 * 
 */
class Rest
{
	const AUTH_TYPE_BEARER = 'Bearer';

	const AUTH_TYPE_BASIC = 'Basic';

	protected $typeAuth;

	protected $token;

	protected $username;

	protected $password;

	protected $sandbox = true;

	private $_url;

	private $_request;

	private $_response;

	private $_info;

	public function getRequest()
	{
		return $this->_request;
	}

	public function getResponse()
	{
		return $this->_response;
	}

	public function getInfo()
	{
		return $this->_info;
	}

	/**
	 * @param  string $uri
	 * @param  string $auth
	 * @param  array $options
	 * @return mixed
	 * @throws
	 */
	protected function _request(string $uri, array $options = array())
	{

    	try{
			$curl = null;
			$this->_request = null;
			$this->_response = null;
			$this->_info = null;
			$this->_url = $uri;
    		
    		$curl = curl_init();
    		curl_setopt_array($curl, $this->_request = $this->_optionsMerge($options));
            
    		$response = curl_exec($curl);
            $response_to_object = json_decode($response);

            if(!empty($response_to_object)) {
                $response = $response_to_object;
                unset($response_to_object);
            }

            $this->_response = $response;
    		
            $info = (object) curl_getinfo($curl);

            $this->_info = $info;

            if($errorMsg = curl_error($curl)) throw new Exception((isset($info) ? $info->http_code : "") . " {$errorMsg}", array(), (int) $info->http_code);
            
            if(!preg_match("/200|201|204|304/", $info->http_code)) {
            	throw new Exception($response->result->Description ?? 'Error', is_array($response) ? $response : array(), (int) $info->http_code);
            }

            return $response;
    	}catch(Exception $e) {
    		$errorList = array(
    			'request' => $this->getRequest(),
    			'response' => $this->getResponse(),
    			'info' => $this->getInfo(),
    			'errors' => $e->getErrors()
    		); 

    		throw new Exception("RESTMagento : {$e->getMessage()}", $errorList, $e->getCode(), $e->getPrevious());
    	}finally {
    		if(!is_null($curl)) curl_close($curl);
    	}
	}

	/**
	 * Adecua los parametros de la peticion.
	 * 
	 * 
	 * @param  string $auth String de authorizacion para la peticion
	 * @param  array $options Opciones enviados en cada peticion.
	 * @return array Configuracion de la peticion.
	 */
	private function _optionsMerge(array $options = array()) : array
    {
    	$options_merge = $this->_optionsDefault();

    	if(count($options) > 0) {
    		foreach ($options as $key => $value) {
    			switch ($key) {
    				case "method":
    					$key = CURLOPT_CUSTOMREQUEST;
    					break;
    				case "data":
    					$key = CURLOPT_POSTFIELDS;
                        // Codifica la data de array a formato json string.
    					$value = json_encode($value, JSON_UNESCAPED_UNICODE);
    					break;
    				default:
    					$key = null;
    					break;
    			}

    			if(!is_null($key)) $options_merge[$key] = $value;
    		}
    	}

    	return $options_merge;
    }

	/**
	 * Genera las configuracion para la peticion REST
	 * @param  string $auth authorization connect
	 * @return array
	 */
	private function _optionsDefault() : array
	{
		$token = $this->typeAuth === self::AUTH_TYPE_BEARER ? $this->token : base64_encode("{$this->username}:{$this->password}");

		return array(
    		CURLOPT_URL => $this->_url,
    		CURLOPT_RETURNTRANSFER => true,
    		CURLOPT_SSL_VERIFYHOST => false,
    		CURLOPT_SSL_VERIFYPEER => false,
    		CURLOPT_ENCODING => "",
    		CURLOPT_MAXREDIRS => 10,
    		CURLOPT_TIMEOUT => 30,
    		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    		CURLOPT_CUSTOMREQUEST => "GET",
    		CURLOPT_HTTPHEADER => array(
    			"Content-Type: application/json",
    			"Authorization: {$this->typeAuth} {$token}"
    		)
    	);
	}
}