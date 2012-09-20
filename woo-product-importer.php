<?php /*
    Plugin Name: Woo Product Importer
    Plugin URI: http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/
    Description: Free CSV import utility for WooCommerce
    Version: .0.1
    Author: Daniel Grundel, Web Presence Partners
    Author URI: http://www.webpresencepartners.com
*/
    
    class WebPres_Woo_Product_Importer {
        
        public function __construct() {
            add_action('admin_menu', array('WebPres_Woo_Product_Importer', 'admin_menu'));
        }
        
        public function admin_menu() {
            add_management_page('Woo Product Importer', 'Woo Product Importer', 'manage_options', 'woo-product-importer', array('WebPres_Woo_Product_Importer', 'render_action'));
        }
        
        public function render_action() {
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'upload';
            require_once('woo-product-importer-common.php');
            require_once("woo-product-importer-{$action}.php");
        }
        
    }
    
    $webpres_woo_product_importer = new WebPres_Woo_Product_Importer();
?>