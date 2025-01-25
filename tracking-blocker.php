<?php
/**
 * Plugin Name: Tracking Blocker for WooCommerce
 * Description: Blocks outbound tracking requests to tracking.woocommerce.com and logs the data.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Blocks requests to tracking.woocommerce.com and logs the data
 *
 * @param array  $preempt  Whether to preempt an HTTP request's return value. Default false.
 * @param array  $parsed_args HTTP request arguments.
 * @param string $url The request URL.
 * @return array|false
 */
function tbwc_block_tracking_requests( $preempt, $parsed_args, $url ) {
    if ( strpos( $url, 'tracking.woocommerce.com/v1/' ) !== false ) {
        // Log the data that was supposed to be sent
        error_log( 'Blocked WooCommerce tracking request: ' . print_r( $parsed_args, true ) );
        
        // Return a response indicating the request was blocked
        return [
            'headers'  => [],
            'body'     => '',
            'response' => [
                'code'    => 403,
                'message' => 'Forbidden'
            ],
            'cookies'  => [],
            'filename' => null
        ];
    }

    return $preempt;
}
add_filter( 'pre_http_request', 'tbwc_block_tracking_requests', 10, 3 );

/**
 * Enqueue admin styles
 */
function tbwc_enqueue_admin_styles() {
    $css = "
        /* Mobile-first styles */
        .tbwc-notice {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid transparent;
            border-radius: .25rem;
        }

        @media (min-width: 768px) {
            .tbwc-notice {
                padding: 20px;
            }
        }
    ";
    wp_add_inline_style( 'wp-admin', $css );
}
add_action( 'admin_enqueue_scripts', 'tbwc_enqueue_admin_styles' );

/**
 * Display admin notice
 */
function tbwc_admin_notice() {
    ?>
    <div class="notice notice-error tbwc-notice">
        <p><?php esc_html_e( 'Tracking Blocker for WooCommerce is active. Outbound tracking requests are blocked.', 'tracking-blocker-woocommerce' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'tbwc_admin_notice' );
