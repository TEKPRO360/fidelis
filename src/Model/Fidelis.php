<?php
namespace Fidelis\Model;

use Fidelis\Exception\Exception;
use Fidelis\Driver\Rest;

/**
 * 
 */
class Fidelis extends Rest
{
	CONST URL_TOKEN_TEST = 'https://apirest-dot-fi-gcp-prom-dev.uk.r.appspot.com/token';
	CONST URL_TOKEN = '';
	
	CONST URL_VALIDA_TEST = 'https://appvalidacupon-dot-fi-gcp-prom-dev.uk.r.appspot.com/vcoupon';
	CONST URL_VALIDA = '';
	
	CONST URL_REDIMIR_TEST = 'https://srvburncoupon-dot-fi-gcp-prom-dev.uk.r.appspot.com/burncoupon';
	CONST URL_REDIMIR = '';

	CONST LOGIN_TEST_USER = 'comercio@comercio.cl';
	CONST LOGIN_TEST_PASSWORD = 'VZz9mB2ag5uAvqS';

	CONST AUTH_BASIC_TEST_USER = 'pxYCEbhb5FyvaPm2vKA85FcNn6Ften3AkDW';
	CONST AUTH_BASIC_TEST_PASSWORD = 'RvXtERmYfcEXSCduUiVgAgXej';

	public function __construct(string $username = '', string $password = '', bool $sandbox = true)
	{
		$this->username = !empty($username) ? $username : self::AUTH_BASIC_TEST_USER;
		$this->password = !empty($password) ? $password : self::AUTH_BASIC_TEST_PASSWORD;
		$this->sandbox = $sandbox;
	}

	public function setCredentials(string $username, string $password)
	{
		$this->username = !empty($username) ? $username : self::AUTH_BASIC_TEST_USER;
		$this->password = !empty($password) ? $password : self::AUTH_BASIC_TEST_PASSWORD;
	}

	public function setSandbox(bool $sandbox = true)
	{
		$this->sandbox = $sandbox;
	}

	private function generateToken()
	{
		if(!empty($this->token)) return $this->token;

		$this->typeAuth = self::AUTH_TYPE_BASIC;

		$access = $this->_request($this->sandbox ? self::URL_TOKEN_TEST : self::URL_TOKEN, array(
			'method' => 'POST',
			'data' => array(
				'usuario' => self::LOGIN_TEST_USER,
				'clave' => self::LOGIN_TEST_PASSWORD
			)
		));

		if($access->status == 0) throw new \Exception($access->result->Description ?? "Credenciales no validas.", $access->result->Code ?? 401);

		return $this->token = $access->result->access_token;
	}

	public function getDatosCupon(array $cupon)
	{
		$this->generateToken();

		$this->typeAuth = self::AUTH_TYPE_BEARER;

		$cupon = $this->_request($this->sandbox ? self::URL_VALIDA_TEST : self::URL_VALIDA, array(
			'method' => 'POST',
			'data' => $cupon
		));

		if($cupon->status == 0) throw new \Exception($cupon->result->Description ?? 'Error en cupon.', $cupon->result->estadoSalida ?? 500);
		
		return $cupon->result;
	}

	public function getRedimirCupon(array $cupon)
	{
		$this->generateToken();

		$this->typeAuth = self::AUTH_TYPE_BEARER;

		$cupon = $this->_request($this->sandbox ? self::URL_REDIMIR_TEST : self::URL_REDIMIR, array(
			'method' => 'POST',
			'data' => $cupon
		));

		if($cupon->status == 0) throw new \Exception($cupon->result->Description ?? 'Error en cupon.', $cupon->result->estadoSalida ?? 500);

		return $cupon->result;
	}
}