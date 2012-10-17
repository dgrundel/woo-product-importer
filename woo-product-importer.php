<?php /*
    Plugin Name: Woo Product Importer
    Plugin URI: http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/
    Description: Free CSV import utility for WooCommerce
    Version: 1
    Author: Daniel Grundel, Web Presence Partners
    Author URI: http://www.webpresencepartners.com
*/
    
    class WebPres_Woo_Product_Importer {
        
        public function __construct() {
            add_action('admin_menu', array('WebPres_Woo_Product_Importer', 'admin_menu'));
            add_action('wp_ajax_woo-product-importer-ajax', array('WebPres_Woo_Product_Importer', 'render_ajax_action'));
        }
        
        public function admin_menu() {
            add_management_page('Woo Product Importer', 'Woo Product Importer', 'manage_options', 'woo-product-importer', array('WebPres_Woo_Product_Importer', 'render_admin_action'));
        }
        
        public function render_admin_action() {
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'upload';
            require_once(plugin_dir_path(__FILE__).'woo-product-importer-common.php');
            require_once(plugin_dir_path(__FILE__)."woo-product-importer-{$action}.php");
        }
        
        public function render_ajax_action() {
            require_once(plugin_dir_path(__FILE__)."woo-product-importer-ajax.php");
            die(); // this is required to return a proper result
        }
    }
    
    $webpres_woo_product_importer = new WebPres_Woo_Product_Importer();
?>