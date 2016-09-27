<?php

namespace runcommand\Doctor\Checks;

use WP_CLI;

/**
 * Check whether autoloaded options are under a specific size.
 */
class Autoload_Options_Size extends Check {

	/**
	 * Threshold in kilobytes
	 */
	protected $threshold_kb = 900;

	public function run() {
		ob_start();
		WP_CLI::run_command( array( 'option', 'list' ), array( 'autoload' => 'on', 'format' => 'total_bytes' ) );
		$total_bytes = (int) ob_get_clean();

		$threshold_bytes = $this->threshold_kb * 1024;
		$human_threshold = self::format_bytes( $threshold_bytes );
		$human_total = self::format_bytes( $total_bytes );
		if ( $threshold_bytes < $total_bytes ) {
			$this->status = 'warning';
			$this->message = "Autoloaded options size ({$human_total}) exceeds threshold ({$human_threshold})";
		} else {
			$this->status = 'success';
			$this->message = "Autoloaded options size ({$human_total}) is less than threshold ({$human_threshold}).";
		}

	}

	private static function format_bytes( $size, $precision = 2 ) {
		$base = log( $size, 1024 );
		$suffixes = array('', 'kb', 'mb', 'g', 't');
		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}

}