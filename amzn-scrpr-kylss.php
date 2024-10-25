<?php

/**
 * Plugin Name:       Amzn Scrpr Kylss
 * Plugin URI:        https://leogopal.com
 * Description:       Amazon Keyless Scraper
 * Version:           0.0.2
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

if (!defined('ABSPATH')) {
    die;
}

define('AMZN_SCRPR_PLUGIN_FILE', __FILE__);
define('AMZN_SCRPR_PLUGIN_SLUG', basename(__FILE__, '.php'));
define('AMZN_SCRPR_PLUGIN_VERSION', '0.0.2');
define('AMZN_SCRPR_PLUGIN_URL', plugin_dir_url(AMZN_SCRPR_PLUGIN_FILE));
define('AMZN_SCRPR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMZN_SCRPR_API', 'babe742a0e34723b70cb4d5b316ca2d3');
define('AMZN_SCRPR_SEARCH', 'https://api.scraperapi.com/structured/amazon/search');
define('AMZN_SCRPR_PRODUCT', 'https://api.scraperapi.com/structured/amazon/product');
define('AMZN_SCRPR_OFFERS', 'https://api.scraperapi.com/structured/amazon/offers');
define('AMZN_SCRPR_COUNTRY', 'br');
define('AMZN_SCRPR_TLD', 'com.br');

/**
 * Fetch data from Amazon API endpoint with caching.
 *
 * @param string $endpoint API endpoint to request.
 * @param array $params Parameters for the API request.
 * @param int $cache_time Cache duration in seconds (default 1 hour).
 * @return array|string Retrieved data or error message.
 */
function amzn_fetch_data($endpoint, $params = array(), $cache_time = 3600)
{
    $cache_key = 'amzn_data_' . md5($endpoint . serialize($params));
    $cached_data = get_transient($cache_key);
    if ($cached_data !== false) {
        return $cached_data;
    }

    $params = wp_parse_args($params, array(
        'api_key' => AMZN_SCRPR_API,
        'country' => AMZN_SCRPR_COUNTRY,
        'tld' => AMZN_SCRPR_TLD,
        'keep_headers' => false,
        'device_type' => 'desktop',
        'premium' => false,
        'render' => false,
        'session_number' => 1,
        'autoparse' => false,
        'retry' => 1,
        'timeout' => 70,
    ));

    $url = add_query_arg($params, $endpoint);
    $response = wp_remote_get($url, array('sslverify' => false));

    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    set_transient($cache_key, $data, $cache_time);

    return $data;
}

/**
 * Display Amazon products fetched from the API in HTML format.
 */
function amzn_display_products()
{
    $products = amzn_fetch_data(AMZN_SCRPR_SEARCH, ["query" => "Iridescent Sunset Rising"]);

    if (is_array($products) && isset($products['results'])) {
        // Start output buffer
        ob_start(); ?>

        <div class="aawp">

            <?php foreach ($products['results'] as $product):
                // var_dump($product);
                // wp_die();
            ?>

                <div class="aawp-product aawp-product--horizontal aawp-product--ribbon aawp-product--sale" data-aawp-product-asin="<?php echo $product['asin']; ?>" data-aawp-product-id="" data-aawp-tracking-id="hostinger084-20" data-aawp-product-title="<?php echo $product['name']; ?>" data-aawp-geotargeting="true" data-aawp-click-tracking="asin" data-aawp-local-click-tracking="1">

                    <span class="aawp-product__ribbon aawp-product__ribbon--sale">Sale</span>
                    <div class="aawp-product__thumb">
                        <a class="aawp-product__image-link" href="<?php echo $product['url'] ?>" title="<?php echo $product['name'] ?>" rel="nofollow noopener sponsored" target="_blank">
                            <img decoding="async" class="aawp-product__image" src="<?php echo $product['image'] ?>" alt="m">
                        </a>
                    </div>

                    <div class="aawp-product__content">
                        <a class="aawp-product__title" href="<?php echo $product['url'] ?>" title="<?php echo $product['name'] ?>" rel="nofollow noopener sponsored" target="_blank">
                            <?php echo $product['name'] ?>
                        </a>
                        <div class="aawp-product__description">
                            <span>ASIN: <?php echo $product['asin']; ?></span>
                        </div>
                    </div>

                    <div class="aawp-product__footer">

                        <div class="aawp-product__pricing">

                            <span class="aawp-product__price aawp-product__price--current"><?php $price = !empty($product['price_string']) ? $product['price_string'] : '';
                                                                                            echo $price; ?></span>

                            <a href="https://www.amazon.com/gp/prime/?tag=hostinger084-20" title="Amazon Prime"
                                rel="nofollow noopener sponsored" target="_blank" class="aawp-check-prime"
                                <img decoding="async"
                                src="https://ams.local/wp-content/plugins/aawp/assets/img/icon-check-prime.svg" height="16"
                                width="55" alt="Amazon Prime">
                            </a>
                        </div>

                        <a class="aawp-button aawp-button--buy aawp-button--icon aawp-button--icon-black" href="<?php echo $product['url'] ?>"
                            title="Buy on Amazon" target="_blank" rel="nofollow noopener sponsored">Buy on Amazon</a>
                    </div>

                </div>

            <?php endforeach; ?>

        </div><?php
                $display = ob_get_clean();
                ob_end_clean();
                // Return the output buffer content
                return $display;
            } else {
                echo '<p>No products found or unable to retrieve data.</p>';
            }
        }

        // Add shortcode for displaying products
        add_shortcode('amzn_products', 'amzn_display_products');
