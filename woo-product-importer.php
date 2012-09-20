<?php /*
    Plugin Name: Woo Product Importer
    Plugin URI: http://webpresencepartners.com
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
        
        public static function generate_slug($str, $maxLength = 50)
        {
            $result = strtolower($str);
            
            $result = preg_replace("/[^a-z0-9\s-]/", "", $result);
            $result = trim(preg_replace("/[\s-]+/", " ", $result));
            $result = trim(substr($result, 0, $maxLength));
            $result = preg_replace("/\s/", "-", $result);
            
            return $result;
        }
        
        public static function clean_number($num) {
            return preg_replace("/[^0-9,.]/", "", $num);
        }
        
        public static function strip_extra_whitespace($content) {
            
            $content = trim($content);
            
            //remove line breaks
            $content = str_replace("\n", ' ', $content );
            $content = str_replace("\r", ' ', $content );
            
            //remove repeating spaces
            $content = preg_replace('/(?:\s\s+|\n|\t)/', ' ', $content);
            
            return $content;
        }
        
    }
    
    $webpres_woo_product_importer = new WebPres_Woo_Product_Importer();
?>