<?php
namespace App\Infrastructure;

class ServiceResponse {
	public $IsSuccess;

	public function __construct($IsSuccess = false){
		$this->IsSuccess = $IsSuccess;
	}
}