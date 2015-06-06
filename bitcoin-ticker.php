<?php
/**
 * Plugin name: Epic Bitcoin ticker
 * Description: Bitcoin Value widget, dashboard widget, and shortcode
 * Author: Nikhil Vimal
 * Author URI: http://nik.techvoltz.com
 * Version: 1.0
 * Plugin URI:
 * License: GNU GPLv2+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

Class Epic_Bitcoin_ticker {

	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'epic_bitcoin_ticker_dashboard_widget' ));
		add_shortcode('epic-bitcoin-ticker', array( $this, 'epic_bitcoin_ticker_shortcode' ));
	}

	/**
	 * The main BPI Value Function
	 */
	public function epic_bitcoin_ticker_function() {
		if ( ! $output_USD = get_transient('epic_bitcoin_ticker_USD') ) {

			// If there's no cached version, let's get a rate
			$jsonurl = "http://api.coindesk.com/v1/bpi/currentprice.json";
			$json = wp_remote_get( $jsonurl );
			if ( is_wp_error( $json ) ) {
				return "BPI not found :(";
			}
			else {

				// If everything's okay, parse the body and json_decode it
				$json_output = json_decode( wp_remote_retrieve_body( $json ));
				$output_USD = 'USD: $' . $json_output->bpi->USD->rate;
				//$output_EUR = 'EUR: '  . $json_output->bpi->EUR->rate . ' Euros';
				//$output_GBP = 'GBP: '  . $json_output->bpi->GBP->rate . ' Pounds';


				// Store the result in a transient, expires after 1 minute
				set_transient( 'epic_bitcoin_ticker_USD', $output_USD, 60 * 1 );
				//set_transient( 'epic_bitcoin_ticker_EUR', $output_EUR, 60 * 1 );
				//set_transient( 'epic_bitcoin_ticker_GBP', $output_GBP, 60 * 1 );


			}
		}

		echo esc_html( $output_USD ) . '</p>';
		//echo esc_html( $output_EUR ) . '</p>';
		//echo esc_html( $output_GBP ) . '</p>';
		echo '<p><strong>Check back often for a new index</strong></p>';
	}

	// The shortcode function for [epic-bitcoin-ticker]
	public function epic_bitcoin_ticker_shortcode() {
		ob_start();

		return $this->epic_bitcoin_ticker_function();

		return ob_get_clean();
	}

	/**
	 * Add dashboard widget.
	 */
	public function epic_bitcoin_ticker_dashboard_widget() {
		wp_add_dashboard_widget(
			'epic_bitcoin_ticker_dashboard_widget', // Widget slug.
			'Bitcoin Value',
			array( $this, 'bitcoin_ticker_widget_function' )
		);
	}

	/**
	 * Callback for dashboard widget
	 */
	public function bitcoin_ticker_widget_function() {
		return $this->epic_bitcoin_ticker_function();
	}

}
new Epic_Bitcoin_ticker();