<?php
namespace Fidelis\Exception;

/**
 * 
 */
class Exception extends \Exception
{
	protected $errors;

	function __construct(string $message, array $errors = array(), int $code = 0, \Throwable $previous = null)
	{
		$this->errors = $errors;

		parent::__construct($message, $code, $previous);
	}

	public function getErrors() : array
	{
		return $this->errors ?? array();
	}
}