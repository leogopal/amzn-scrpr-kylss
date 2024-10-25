<?php

/**
 * Plugin Name:       Amzn Scrpr Kylss
 * Plugin URI:        https://leogopal.com
 * Description:       Amazon Keyless Scraper
 * Version:           0.0.1
 * Author:            leogopal
 * Requires PHP:      8.0
 * Requires at least: 5.9
 * Tested up to:      6.5
 * Author URI:        https://hostinger.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amzn-scrpr-kylss
 * Domain Path:       /languages
 *
 * @package AmazonKeylessScraper
 */

/**
 * Avoid possibility to get file accessed directly
 */
if (! defined('ABSPATH')) {
    die;
}

/**
 * Path to plugin file
 */
define('AMZN_SCRPR_PLUGIN_FILE', __FILE__);

/**
 * Plugin slug
 */
define('AMZN_SCRPR_PLUGIN_SLUG', basename(__FILE__, '.php'));

/**
 * Plugin version
 */
define('AMZN_SCRPR_PLUGIN_VERSION', '0.0.1');

/**
 * Plugin URL
 */
define('AMZN_SCRPR_PLUGIN_URL', plugin_dir_url(AMZN_SCRPR_PLUGIN_FILE));

/**
 * Plugin directory
 */
define('AMZN_SCRPR_PLUGIN_DIR', plugin_dir_path(__FILE__));

define('AMZN_SCRPR_API', 'f41b2eb0a9ff727762fcc8464edea450');

define('AMZN_SCRPR_Search', 'https://api.scraperapi.com/structured/amazon/search');
define('AMZN_SCRPR_Product', 'https://api.scraperapi.com/structured/amazon/product');
define('AMZN_SCRPR_Offers', 'https://api.scraperapi.com/structured/amazon/offers');
define('AMZN_SCRPR_COUNTRY', 'us');
define('AMZN_SCRPR_TLD', 'com');

function cwpai_fetch_amazon_data($endpoint, $params = array())
{

    $params = wp_parse_args($params, array(
        'api_key' => AMZN_SCRPR_API,
        'country_code' => AMZN_SCRPR_COUNTRY,
        'tld' => AMZN_SCRPR_TLD,
        'headers' => [],
        'device_type' => 'desktop',
        'premium' => true,
        'render' => true,
        'session_number' => 1,
        'autoparse' => true,
        'retry' => 3,
        'timeout' => 60,
    ));
    $url = add_query_arg($params, $endpoint);

    $response = wp_remote_get($url, array('sslverify' => false));

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    }

    return wp_remote_retrieve_body($response);
}

$response = cwpai_fetch_amazon_data(AMZN_SCRPR_Search, array('query' => 'Rising while falling down'));
print_r($response);
