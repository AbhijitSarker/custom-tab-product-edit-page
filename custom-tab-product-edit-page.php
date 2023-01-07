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
// add_filter('woocommerce_product_data_tabs', 'wk_custom_product_tab', 10, 1);

// function wk_custom_product_tab($default_tabs)
// {
//     $default_tabs['custom_tab'] = array(
//         'label'   =>  __('Additional Info', 'domain'),
//         'target'  =>  'wk_custom_tab_data',
//         'priority' => 60,
//         'class'   => array()
//     );
//     return $default_tabs;
// }

// add_action('woocommerce_product_data_panels', 'wk_custom_tab_data');
// function wk_custom_tab_data()
// {
//     echo '<div id="wk_custom_tab_data" class="panel woocommerce_options_panel">// add content here
//     <input type="text">

//     </div>';
// }



// method 1 
// Add a new 'Care Information' tab to Product Data area of Edit Product page.
add_filter('woocommerce_product_data_tabs', 'dcwd_care_instruction_product_data_tab');
function dcwd_care_instruction_product_data_tab($tabs)
{
    $tabs['where'] = array(
        'label'    => 'Care Information',
        'target'   => 'care_instruction_product_data',
        'priority' => 90,
    );
    return $tabs;
}


// Return an array of the options. This is used in multiple functions and
// is used to validate/sanitise the submitted value.
function dcwd_care_instructions()
{
    $care_instructions = array(
        'cold'  => 'Wash at 30&deg;C',
        'warm'  => 'Wash at 40&deg;C',
        'hot'   => 'Wash at 60&deg;C',
    );
    return $care_instructions;
}


// Add the Delivery Option tab contents.
add_action('woocommerce_product_data_panels', 'care_instruction_data_panel', 100);
function care_instruction_data_panel()
{
?>
    <div id="care_instruction_product_data" class="panel woocommerce_options_panel">
        <div class="options_group care_instruction">
            <?php
            woocommerce_wp_radio(array(
                'id'            => '_care_instruction',
                'label'         => 'Care instruction',
                'options'       => dcwd_care_instructions(),
            ));
            ?>
        </div>
    </div>
<?php
}


// Save Care Information radio field to post meta.
// Save code is based on: https://businessbloomer.com/woocommerce-display-rrp-msrp-manufacturer-price/
add_action('save_post_product', 'dcwd_save_care_instruction');
function dcwd_save_care_instruction($product_id)
{
    global $pagenow, $typenow;
    if ('post.php' !== $pagenow || 'product' !== $typenow) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['_care_instruction'])) {
        if ($_POST['_care_instruction']) {
            $care_instructions = dcwd_care_instructions();
            // Verify that the submitted value is a valid one.
            if (array_key_exists($_POST['_care_instruction'], $care_instructions)) {
                update_post_meta($product_id, '_care_instruction', $_POST['_care_instruction']);
            }
        }
    } else {
        delete_post_meta($product_id, '_care_instruction');
    }
}


// Add to the "Additional Information" tab on the single product page.
add_action('woocommerce_product_additional_information', 'dcwd_display_care_instruction', 10);
function dcwd_display_care_instruction($product)
{
    if ('variable' != $product->get_type() && $care_instruction = get_post_meta($product->get_id(), '_care_instruction', true)) {
        $care_instructions = dcwd_care_instructions();
        printf('<p>Care instruction: %s</p>', $care_instructions[$care_instruction]);
    }
}
///end




//method 2
// -----------------------------------------
// 1. Add RRP field input @ product edit page

add_action('woocommerce_product_options_pricing', 'bbloomer_add_RRP_to_products');

function bbloomer_add_RRP_to_products()
{
    woocommerce_wp_text_input(array(
        'id' => 'rrp',
        'class' => 'short wc_input_price',
        'label' => __('RRP', 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
        'data_type' => 'price',
    ));
}

// -----------------------------------------
// 2. Save RRP field via custom field

add_action('save_post_product', 'bbloomer_save_RRP');

function bbloomer_save_RRP($product_id)
{
    global $typenow;
    if ('product' === $typenow) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (isset($_POST['rrp'])) {
            update_post_meta($product_id, 'rrp', $_POST['rrp']);
        }
    }
}

// -----------------------------------------
// 3. Display RRP field @ single product page

add_action('woocommerce_single_product_summary', 'bbloomer_display_RRP', 9);

function bbloomer_display_RRP()
{
    global $product;
    if ($product->get_type() <> 'variable' && $rrp = get_post_meta($product->get_id(), 'rrp', true)) {
        echo '<div class="woocommerce_rrp">';
        _e('RRP: ', 'woocommerce');
        echo '<span>' . wc_price($rrp) . '</span>';
        echo '</div>';
    }
}

