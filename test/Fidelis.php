<?php
namespace Fidelis\Test;

require_once __DIR__ . '/../vendor/autoload.php';

use Fidelis\Model\Fidelis;
use Fidelis\Exception\Exception;

try {
	$fidelis = new Fidelis();

	$cupon = $fidelis->getRedimirCupon(array(
		'codigo' => 'vAMGST2qLL',
		'rut' => '174872961',

		"tipo_trx" => "0",
	    "tipo_prod" => "0",
	    "monto_total" => "0",
	    "monto_beneficio" => "0",
	    "monto_copago" => "0",
	    "numero_boleta" => "0",
	    "codigo_prod" => "0",
	    "desc_prod" => "0",
	    "codigo_suc_origen" => "0",
	    "desc_suc_origen" => "0",
	    "codigo_comuna" => "0",
	    "desc_comuna" => "0",
	    "codigo_canal" => "0",

	    "numero_item" => "0",
	    "division_prod" => "0",
	    "codigo_ciudad" => "0",
	    "desc_ciudad" => "0",
	    "codigo_region" => "0",
	    "desc_region" => "0"
	));

	var_dump($cupon);
}catch(Exception $fe) {
	var_dump($fe->getErrors(), $fe->getMessage());
}catch(\Exception $e) {
	var_dump('E', $e->getMessage());
}