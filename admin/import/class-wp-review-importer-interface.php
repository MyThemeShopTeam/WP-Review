<?php

interface WP_Review_Importer_Interface {

	/**
	 *
	 * @param int $numposts
	 * @param int $offset
	 * @param array $options
	 *
	 * @return WP_Review_Importer_Response
	 */
	public function run( $numposts, $offset, $options );
}
