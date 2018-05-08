<?php

class NodeValidator {
	var $validationErrors = array();

	function getValidationError($key) {
		$errorMsg = $this->validationErrors[$key];
		return $errorMsg."";
	}

	function setValidationError($key, $msg) {
		$this->validationErrors[$key] = $msg;
	}

	function getValidationErrorsCount() {
		return sizeof(validationErrors);
	}

	function getValidationErrors() {
		return $this->validationErrors;
	}
}

?>