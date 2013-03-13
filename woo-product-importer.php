<?php /*
    Plugin Name: Woo Product Importer
    Plugin URI: http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/
    Description: Free CSV import utility for WooCommerce
    Version: 1
    Author: Daniel Grundel, Web Presence Partners
    Author URI: http://www.webpresencepartners.com
    Text Domain: woo-product-importer
    Domain Path: /languages/
*/

/*
    This file is part of Woo Product Importer.
    
    Woo Product Importer is Copyright 2012-2013 Web Presence Partners LLC.

    Woo Product Importer is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    Woo Product Importer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.
    
    You should have received a copy of the GNU Lesser General Public License
    along with Woo Product Importer.  If not, see <http://www.gnu.org/licenses/>.
*/
    
    class WebPres_Woo_Product_Importer {
        
        public function __construct() {
            add_action( 'init', array( 'WebPres_Woo_Product_Importer', 'translations' ), 1 );
            add_action('admin_menu', array('WebPres_Woo_Product_Importer', 'admin_menu'));
            add_action('wp_ajax_woo-product-importer-ajax', array('WebPres_Woo_Product_Importer', 'render_ajax_action'));
        }

        public function translations() {
            load_plugin_textdomain( 'woo-product-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public function admin_menu() {
            add_management_page( __( 'Woo Product Importer', 'woo-product-importer' ), __( 'Woo Product Importer', 'woo-product-importer' ), 'manage_options', 'woo-product-importer', array('WebPres_Woo_Product_Importer', 'render_admin_action'));
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