<?php

class WP_Review_Importer_Response {

	private $message;

	private $is_done;

	private $offset;

	private $is_error;

	public function __construct( $message, $is_done = true, $offset = 0, $is_error = false ) {
		$this->message = $message;
		$this->is_done = $is_done;
		$this->offset = $offset;
		$this->is_error = $is_error;
	}

	public function to_array() {
		return array(
			'message' => $this->message,
			'is_done' => $this->is_done,
			'offset'  => $this->offset,
			'is_error' => $this->is_error,
		);
	}

	public function is_error() {
		return $this->is_error;
	}
}
