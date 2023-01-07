<?php

/**
 * Plugin Name:       Product Edit Page Custom Tab
 * Plugin URI:        https://github.com/AbhijitSarker
 * Description:       Add custom tabs in the product edit page.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Abhijit Sarker
 * Author URI:        https://github.com/AbhijitSarker
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    die;
}

// add_action('init', 'my_rewrite_flush');
// function my_rewrite_flush()
// {

//     flush_rewrite_rules();
// }

//add custom tabs in single product
add_filter('woocommerce_product_data_tabs', 'wk_custom_product_tab', 10, 1);

function wk_custom_product_tab($default_tabs)
{
    $default_tabs['custom_tab'] = array(
        'label'   =>  __('Custom Tab', 'domain'),
        'target'  =>  'wk_custom_tab_data',
        'priority' => 60,
        'class'   => array()
    );
    return $default_tabs;
}

add_action('woocommerce_product_data_panels', 'wk_custom_tab_data');
function wk_custom_tab_data()
{
    echo '<div id="wk_custom_tab_data" class="panel woocommerce_options_panel">// add content here
    <input type="text">

    </div>';
}

