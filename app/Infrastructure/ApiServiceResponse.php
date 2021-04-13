<?php
namespace App\Infrastructure;

class ApiServiceResponse {
	public $IsSuccess;

	public function __construct($IsSuccess = false){
		$this->IsSuccess = $IsSuccess;
	}
}