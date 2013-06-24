<?php /*
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

    ini_set("auto_detect_line_endings", true);

    $post_data = array(
        'uploaded_file_path' => $_POST['uploaded_file_path'],
        'header_row' => $_POST['header_row'],
        'limit' => $_POST['limit'],
        'offset' => $_POST['offset'],
        'map_to' => maybe_unserialize(stripslashes($_POST['map_to'])),
        'custom_field_name' => maybe_unserialize(stripslashes($_POST['custom_field_name'])),
        'custom_field_visible' => maybe_unserialize(stripslashes($_POST['custom_field_visible'])),
        'product_image_set_featured' => maybe_unserialize(stripslashes($_POST['product_image_set_featured'])),
        'product_image_skip_duplicates' => maybe_unserialize(stripslashes($_POST['product_image_skip_duplicates'])),
        'post_meta_key' => maybe_unserialize(stripslashes($_POST['post_meta_key'])),
        'user_locale' => maybe_unserialize(stripslashes($_POST['user_locale'])),
        'import_csv_separator' => maybe_unserialize(stripslashes($_POST['import_csv_separator'])),
        'import_csv_hierarchy_separator' => maybe_unserialize(stripslashes($_POST['import_csv_hierarchy_separator']))
    );

    if(isset($post_data['uploaded_file_path'])) {

        setlocale(LC_ALL, $post_data['user_locale']);

        $error_messages = array();

        //now that we have the file, grab contents
        $temp_file_path = $post_data['uploaded_file_path'];
        $handle = fopen( $temp_file_path, 'r' );
        $import_data = array();

        if ( $handle !== FALSE ) {
            while ( ( $line = fgetcsv($handle, 0, $post_data['import_csv_separator']) ) !== FALSE ) {
                $import_data[] = $line;
            }
            fclose( $handle );
        } else {
            $error_messages[] = __( 'Could not open CSV file.', 'woo-product-importer' );
        }

        if(sizeof($import_data) == 0) {
            $error_messages[] = __( 'No data found in CSV file.', 'woo-product-importer' );
        }

        //discard header row from data set, if we have one
        if(intval($post_data['header_row']) == 1) array_shift($import_data);

        //total size of data to import (not just what we're doing on this pass)
        $row_count = sizeof($import_data);

        //slice down our data based on limit and offset params
        $limit = intval($post_data['limit']);
        $offset = intval($post_data['offset']);
        if($limit > 0 || $offset > 0) {
            $import_data = array_slice($import_data, $offset , ($limit > 0 ? $limit : null), true);
        }

        //a few stats about the current operation to send back to the browser.
        $rows_remaining = ($row_count - ($offset + $limit)) > 0 ? ($row_count - ($offset + $limit)) : 0;
        $insert_count = ($row_count - $rows_remaining);
        $insert_percent = number_format(($insert_count / $row_count) * 100, 1);

        //array that will be sent back to the browser with info about what we inserted.
        $inserted_rows = array();

        //this is where the fun begins
        foreach($import_data as $row_id => $row) {

            //unset new_post_id
            $new_post_id = null;

            //array of imported post data
            $new_post = array();

            //set some defaults in case the post doesn't exist
            $new_post_defaults = array();
            $new_post_defaults['post_type'] = 'product';
            $new_post_defaults['post_status'] = 'publish';
            $new_post_defaults['post_title'] = '';
            $new_post_defaults['post_content'] = '';
            $new_post_defaults['menu_order'] = 0;

            //array of imported post_meta
            $new_post_meta = array();

            //default post_meta to use if the post doesn't exist
            $new_post_meta_defaults = array();
            $new_post_meta_defaults['_visibility'] = 'visible';
            $new_post_meta_defaults['_featured'] = 'no';
            $new_post_meta_defaults['_weight'] = 0;
            $new_post_meta_defaults['_length'] = 0;
            $new_post_meta_defaults['_width'] = 0;
            $new_post_meta_defaults['_height'] = 0;
            $new_post_meta_defaults['_sku'] = '';
            $new_post_meta_defaults['_stock'] = '';
            $new_post_meta_defaults['_sale_price'] = '';
            $new_post_meta_defaults['_sale_price_dates_from'] = '';
            $new_post_meta_defaults['_sale_price_dates_to'] = '';
            $new_post_meta_defaults['_tax_status'] = 'taxable';
            $new_post_meta_defaults['_tax_class'] = '';
            $new_post_meta_defaults['_purchase_note'] = '';
            $new_post_meta_defaults['_downloadable'] = 'no';
            $new_post_meta_defaults['_virtual'] = 'no';
            $new_post_meta_defaults['_backorders'] = 'no';

            //stores tax and term ids so we can associate our product with terms and taxonomies
            //this is a multidimensional array
            //format is: array( 'tax_name' => array(1, 3, 4), 'another_tax_name' => array(5, 9, 23) )
            $new_post_terms = array();

            //a list of woocommerce "custom fields" to be added to product.
            $new_post_custom_fields = array();
            $new_post_custom_field_count = 0;

            //a list of image URLs to be downloaded.
            $new_post_image_urls = array();

            //a list of image paths to be added to the database.
            //$new_post_image_urls will be added to this array later as paths once they are downloaded.
            $new_post_image_paths = array();

            //keep track of any errors or messages generated during post insert or image downloads.
            $new_post_errors = array();
            $new_post_messages = array();

            //track whether or not the post was actually inserted.
            $new_post_insert_success = false;

            foreach($row as $key => $col) {
                $map_to = $post_data['map_to'][$key];

                //skip if the column is blank.
                //useful when two CSV cols are mapped to the same product field.
                //you would do this to merge two columns in your CSV into one product field.
                if(strlen($col) == 0) {
                    continue;
                }

                //validate col value if necessary
                switch($map_to) {
                    case '_downloadable':
                    case '_virtual':
                    case '_manage_stock':
                    case '_featured':
                        if(!in_array($col, array('yes', 'no'))) continue;
                        break;

                    case 'comment_status':
                    case 'ping_status':
                        if(!in_array($col, array('open', 'closed'))) continue;
                        break;

                    case '_visibility':
                        if(!in_array($col, array('visible', 'catalog', 'search', 'hidden'))) continue;
                        break;

                    case '_stock_status':
                        if(!in_array($col, array('instock', 'outofstock'))) continue;
                        break;

                    case '_backorders':
                        if(!in_array($col, array('yes', 'no', 'notify'))) continue;
                        break;

                    case '_tax_status':
                        if(!in_array($col, array('taxable', 'shipping', 'none'))) continue;
                        break;

                    case '_product_type':
                        if(!in_array($col, array('simple', 'variable', 'grouped', 'external'))) continue;
                        break;
                }

                //prepare the col value for insertion into the database
                switch($map_to) {

                    //post fields
                    case 'post_title':
                    case 'post_content':
                    case 'post_excerpt':
                    case 'post_status':
                    case 'comment_status':
                    case 'ping_status':
                        $new_post[$map_to] = $col;
                        break;

                    //integer post fields
                    case 'menu_order':
                        //remove any non-numeric chars
                        $col_value = preg_replace("/[^0-9]/", "", $col);
                        if($col_value == "") continue;

                        $new_post[$map_to] = $col_value;
                        break;

                    //integer postmeta fields
                    case '_stock':
                    case '_download_expiry':
                    case '_download_limit':
                        //remove any non-numeric chars
                        $col_value = preg_replace("/[^0-9]/", "", $col);
                        if($col_value == "") continue;

                        $new_post_meta[$map_to] = $col_value;
                        break;

                    //float postmeta fields
                    case '_weight':
                    case '_length':
                    case '_width':
                    case '_height':
                    case '_regular_price':
                    case '_sale_price':
                        //remove any non-numeric chars except for '.'
                        $col_value = preg_replace("/[^0-9.]/", "", $col);
                        if($col_value == "") continue;

                        $new_post_meta[$map_to] = $col_value;
                        break;

                    //sku
                    case '_sku':
                        $col_value = trim($col);
                        if($col_value == "") continue;

                        $new_post_meta[$map_to] = $col_value;
                        break;

                    //file_path(s)
                    case '_file_path':
                    case '_file_paths':
                        if(!is_array($new_post_meta['_file_paths'])) $new_post_meta['_file_paths'] = array();

                        $new_post_meta['_file_paths'][md5($col)] = $col;
                        break;

                    //all other postmeta fields
                    case '_tax_status':
                    case '_tax_class':
                    case '_visibility':
                    case '_featured':
                    case '_downloadable':
                    case '_virtual':
                    case '_stock_status':
                    case '_backorders':
                    case '_manage_stock':
                    case '_button_text':
                    case '_product_url':
                        $new_post_meta[$map_to] = $col;
                        break;

                    case 'post_meta':
                        $new_post_meta[$post_data['post_meta_key'][$key]] = $col;
                        break;

                    case '_product_type':
                        //product_type is saved as both post_meta and via a taxonomy.
                        $new_post_meta[$map_to] = $col;

                        $term_name = $col;
                        $tax = 'product_type';
                        $term = get_term_by('name', $term_name, $tax, 'ARRAY_A');

                        //if we got a term, save the id so we can associate
                        if(is_array($term)) {
                            $new_post_terms[$tax][] = intval($term['term_id']);
                        }

                        break;

                    case 'product_cat_by_name':
                    case 'product_tag_by_name':
                    case 'product_shipping_class_by_name':
                        $tax = str_replace('_by_name', '', $map_to);
                        $term_paths = explode('|', $col);
                        foreach($term_paths as $term_path) {

                            $term_names = explode($post_data['import_csv_hierarchy_separator'], $term_path);
                            $term_ids = array();

                            for($depth = 0; $depth < count($term_names); $depth++) {

                                $term_parent = ($depth > 0) ? $term_ids[($depth - 1)] : '';
                                $term = term_exists($term_names[$depth], $tax, $term_parent);

                                //if term does not exist, try to insert it.
                                if( $term === false || $term === 0 || $term === null) {
                                    $insert_term_args = ($depth > 0) ? array('parent' => $term_ids[($depth - 1)]) : array();
                                    $term = wp_insert_term($term_names[$depth], $tax, $insert_term_args);
                                    delete_option("{$tax}_children");
                                }

                                if(is_array($term)) {
                                    $term_ids[$depth] = intval($term['term_id']);
                                } else {
                                    //uh oh.
                                    $new_post_errors[] = "Couldn't find or create {$tax} with path {$term_path}.";
                                    break;
                                }
                            }

                            //if we got a term at the end of the path, save the id so we can associate
                            if(array_key_exists(count($term_names) - 1, $term_ids)) {
                                $new_post_terms[$tax][] = $term_ids[(count($term_names) - 1)];
                            }
                        }
                        break;

                    case 'product_cat_by_id':
                    case 'product_tag_by_id':
                    case 'product_shipping_class_by_id':
                        $tax = str_replace('_by_id', '', $map_to);
                        $term_ids = explode('|', $col);
                        foreach($term_ids as $term_id) {
                            //$term = get_term_by('id', $term_id, $tax, 'ARRAY_A');
                            $term = term_exists($term_id, $tax);

                            //if we got a term, save the id so we can associate
                            if(is_array($term)) {
                                $new_post_terms[$tax][] = intval($term['term_id']);
                            } else {
                                $new_post_errors[] = "Couldn't find {$tax} with ID {$term_id}.";
                            }

                        }
                        break;

                    case 'custom_field':
                        $field_name = $post_data['custom_field_name'][$key];
                        $field_slug = sanitize_title($field_name);
                        $visible = intval($post_data['custom_field_visible'][$key]);

                        $new_post_custom_fields[$field_slug] = array (
                            "name" => $field_name,
                            "value" => $col,
                            "position" => $new_post_custom_field_count++,
                            "is_visible" => $visible,
                            "is_variation" => 0,
                            "is_taxonomy" => 0
                        );
                        break;

                    case 'product_image_by_url':
                        $image_urls = explode('|', $col);
                        if(is_array($image_urls)) {
                            $new_post_image_urls = array_merge($new_post_image_urls, $image_urls);
                        }

                        break;

                    case 'product_image_by_path':
                        $image_paths = explode('|', $col);
                        if(is_array($image_paths)) {
                            foreach($image_paths as $image_path) {
                                $new_post_image_paths[] = array(
                                    'path' => $image_path,
                                    'source' => $image_path
                                );
                            }
                        }

                        break;
                }
            }

            //set some more post_meta and parse things as appropriate

            //set price to sale price if we have one, regular price otherwise
            $new_post_meta['_price'] = array_key_exists('_sale_price', $new_post_meta) ? $new_post_meta['_sale_price'] : $new_post_meta['_regular_price'];

            //check and set some inventory defaults
            if(array_key_exists('_stock', $new_post_meta)) {

                //set _manage_stock to yes if not explicitly set by CSV
                if(!array_key_exists('_manage_stock', $new_post_meta)) {
                    $new_post_meta['_manage_stock'] = 'yes';
                }
                //set _stock_status based on _stock if not explicitly set by CSV
                if(!array_key_exists('_stock_status', $new_post_meta)) {
                    //set to instock if _stock is > 0, otherwise set to outofstock
                    $new_post_meta['_stock_status'] = (intval($new_post_meta['_stock']) > 0) ? 'instock' : 'outofstock';
                }

            } else {

                //set _manage_stock to no if not explicitly set by CSV
                if(!array_key_exists('_manage_stock', $new_post_meta)) $new_post_meta['_manage_stock'] = 'no';
            }

            //try to find a product with a matching SKU
            $existing_product = null;
            if(array_key_exists('_sku', $new_post_meta) && !empty($new_post_meta['_sku']) > 0) {
                $existing_post_query = array(
                    'numberposts' => 1,
                    'meta_key' => '_sku',
                    'meta_query' => array(
                        array(
                            'key'=>'_sku',
                            'value'=> $new_post_meta['_sku'],
                            'compare' => '='
                        )
                    ),
                    'post_type' => 'product');
                $existing_posts = get_posts($existing_post_query);
                if(is_array($existing_posts) && sizeof($existing_posts) > 0) {
                    $existing_product = array_shift($existing_posts);
                }
            }

            if(strlen($new_post['post_title']) > 0 || $existing_product !== null) {

                //insert/update product
                if($existing_product !== null) {
                    $new_post_messages[] = sprintf( __( 'Updating product with ID %s.', 'woo-product-importer' ), $existing_product->ID );

                    $new_post['ID'] = $existing_product->ID;
                    $new_post_id = wp_update_post($new_post);
                } else {

                    //merge in default values since we're creating a new product from scratch
                    $new_post = array_merge($new_post_defaults, $new_post);
                    $new_post_meta = array_merge($new_post_meta_defaults, $new_post_meta);

                    $new_post_id = wp_insert_post($new_post, true);
                }

                if(is_wp_error($new_post_id)) {
                    $new_post_errors[] = sprintf( __( 'Couldn\'t insert product with name %s.', 'woo-product-importer' ), $new_post['post_title'] );
                } elseif($new_post_id == 0) {
                    $new_post_errors[] = sprintf( __( 'Couldn\'t update product with ID %s.', 'woo-product-importer' ), $new_post['ID'] );
                } else {
                    //insert successful!
                    $new_post_insert_success = true;

                    //set post_meta on inserted post
                    foreach($new_post_meta as $meta_key => $meta_value) {
                        add_post_meta($new_post_id, $meta_key, $meta_value, true) or
                            update_post_meta($new_post_id, $meta_key, $meta_value);
                    }

                    //set _product_attributes postmeta to the custom fields array. WP will serialize it for us.
                    //first, work on existing attributes
                    if($existing_product !== null) {
                        $existing_product_attributes = get_post_meta($new_post_id, '_product_attributes', true);
                        if(is_array($existing_product_attributes)) {
                            //set the 'position' value for all *new* attributes.
                            $max_position = 0;
                            foreach($existing_product_attributes as $field_slug => $field_data) {
                                $max_position = max(intval($field_data['position']), $max_position);
                            }
                            foreach($new_post_custom_fields as $field_slug => $field_data) {
                                if(!array_key_exists($field_slug, $existing_product_attributes)) {
                                    $new_post_custom_fields[$field_slug]['position'] = ++$max_position;
                                }
                            }
                            $new_post_custom_fields = array_merge($existing_product_attributes, $new_post_custom_fields);
                        }
                    }
                    add_post_meta($new_post_id, '_product_attributes', $new_post_custom_fields, true) or
                        update_post_meta($new_post_id, '_product_attributes', $new_post_custom_fields);

                    //set post terms on inserted post
                    foreach($new_post_terms as $tax => $term_ids) {
                        wp_set_object_terms($new_post_id, $term_ids, $tax);
                    }

                    //figure out where the uploads folder lives
                    $wp_upload_dir = wp_upload_dir();

                    //grab product images
                    foreach($new_post_image_urls as $image_index => $image_url) {

                        //convert space chars into their hex equivalent.
                        //thanks to github user 'becasual' for submitting this change
                        $image_url = str_replace(' ', '%20', trim($image_url));

                        //do some parsing on the image url so we can take a look at
                        //its file extension and file name
                        $parsed_url = parse_url($image_url);
                        $pathinfo = pathinfo($parsed_url['path']);

                        //If our 'image' file doesn't have an image file extension, skip it.
                        $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
                        $image_ext = strtolower($pathinfo['extension']);
                        if(!in_array($image_ext, $allowed_extensions)) {
                            $new_post_errors[] = sprintf( __( 'A valid file extension wasn\'t found in %s. Extension found was %s. Allowed extensions are: %s.', 'woo-product-importer' ), $image_url, $image_ext, implode( ',', $allowed_extensions ) );
                            continue;
                        }

                        //figure out where we're putting this thing.
                        $dest_filename = wp_unique_filename( $wp_upload_dir['path'], $pathinfo['basename'] );
                        $dest_path = $wp_upload_dir['path'] . '/' . $dest_filename;
                        $dest_url = $wp_upload_dir['url'] . '/' . $dest_filename;

                        //download the image to our local server.
                        // if allow_url_fopen is enabled, we'll use that. Otherwise, we'll try cURL
                        if(ini_get('allow_url_fopen')) {
                            //attempt to copy() file show error on failure.
                            if( ! @copy($image_url, $dest_path)) {
                                $http_status = $http_response_header[0];
                                $new_post_errors[] = sprintf( __( '%s encountered while attempting to download %s', 'woo-product-importer' ), $http_status, $image_url );
                            }

                        } elseif(function_exists('curl_init')) {
                            $ch = curl_init($image_url);
                            $fp = fopen($dest_path, "wb");

                            $options = array(
                                CURLOPT_FILE => $fp,
                                CURLOPT_HEADER => 0,
                                CURLOPT_FOLLOWLOCATION => 1,
                                CURLOPT_TIMEOUT => 60); // in seconds

                            curl_setopt_array($ch, $options);
                            curl_exec($ch);
                            $http_status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
                            curl_close($ch);
                            fclose($fp);

                            //delete the file if the download was unsuccessful
                            if($http_status != 200) {
                                unlink($dest_path);
                                $new_post_errors[] = sprintf( __( 'HTTP status %s encountered while attempting to download %s', 'woo-product-importer' ), $http_status, $image_url );
                            }
                        } else {
                            //well, damn. no joy, as they say.
                            $error_messages[] = sprintf( __( 'Looks like %s is off and %s is not enabled. No images were imported.', 'woo-product-importer' ), '<code>allow_url_fopen</code>', '<code>cURL</code>'  );
                            break;
                        }

                        //make sure we actually got the file.
                        if(!file_exists($dest_path)) {
                            $new_post_errors[] = sprintf( __( 'Couldn\'t download file %s.', 'woo-product-importer' ), $image_url );
                            continue;
                        }

                        //whew. are we there yet?
                        $new_post_image_paths[] = array(
                            'path' => $dest_path,
                            'source' => $image_url
                        );
                    }

                    $image_gallery_ids = array();

                    foreach($new_post_image_paths as $image_index => $dest_path_info) {

                        //check for duplicate images, only for existing products
                        if($existing_product !== null && intval($post_data['product_image_skip_duplicates'][$key]) == 1) {
                            $existing_attachment_query = array(
                                'numberposts' => 1,
                                'meta_key' => '_import_source',
                                'post_status' => 'inherit',
                                'post_parent' => $existing_product->ID,
                                'meta_query' => array(
                                    array(
                                        'key'=>'_import_source',
                                        'value'=> $dest_path_info['source'],
                                        'compare' => '='
                                    )
                                ),
                                'post_type' => 'attachment');
                            $existing_attachments = get_posts($existing_attachment_query);
                            if(is_array($existing_attachments) && sizeof($existing_attachments) > 0) {
                                //we've already got this file.
                                $new_post_messages[] = sprintf( __( 'Skipping import of duplicate image %s.', 'woo-product-importer' ), $dest_path_info['source'] );
                                continue;
                            }
                        }

                        //make sure we actually got the file.
                        if(!file_exists($dest_path_info['path'])) {
                            $new_post_errors[] = sprintf( __( 'Couldn\'t find local file %s.', 'woo-product-importer' ), $dest_path_info['path'] );
                            continue;
                        }

                        $dest_url = str_ireplace(ABSPATH, home_url('/'), $dest_path_info['path']);
                        $path_parts = pathinfo($dest_path_info['path']);

                        //add a post of type 'attachment' so this item shows up in the WP Media Library.
                        //our imported product will be the post's parent.
                        $wp_filetype = wp_check_filetype($dest_path_info['path']);
                        $attachment = array(
                            'guid' => $dest_url,
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', $path_parts['filename']),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attachment_id = wp_insert_attachment( $attachment, $dest_path_info['path'], $new_post_id );
                        // you must first include the image.php file
                        // for the function wp_generate_attachment_metadata() to work
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata( $attachment_id, $dest_path_info['path'] );
                        wp_update_attachment_metadata( $attachment_id, $attach_data );

                        //keep track of where the attachment came from so we don't import duplicates later
                        add_post_meta($attachment_id, '_import_source', $dest_path_info['source'], true) or
                            update_post_meta($attachment_id, '_import_source', $dest_path_info['source']);

                        //set the image as featured if it is the first image in the set AND
                        //the user checked the box on the preview page.
                        if($image_index == 0 && intval($post_data['product_image_set_featured'][$key]) == 1) {
                            update_post_meta($new_post_id, '_thumbnail_id', $attachment_id);
                        } else {
                            $image_gallery_ids[] = $attachment_id;
                        }
                    }

                    if(count($image_gallery_ids) > 0) {
                        update_post_meta($new_post_id, '_product_image_gallery', implode(',', $image_gallery_ids));
                    }
                }

            } else {
                $new_post_errors[] = __( 'Skipped import of product without a name', 'woo-product-importer' );
            }

            //this is returned back to the results page.
            //any fields that should show up in results should be added to this array.
            $inserted_rows[] = array(
                'row_id' => $row_id,
                'post_id' => $new_post_id ? $new_post_id : '',
                'name' => $new_post['post_title'] ? $new_post['post_title'] : '',
                'sku' => $new_post_meta['_sku'] ? $new_post_meta['_sku'] : '',
                'price' => $new_post_meta['_price'] ? $new_post_meta['_price'] : '',
                'has_errors' => (sizeof($new_post_errors) > 0),
                'errors' => $new_post_errors,
                'has_messages' => (sizeof($new_post_messages) > 0),
                'messages' => $new_post_messages,
                'success' => $new_post_insert_success
            );
        }
    }

    echo json_encode(array(
        'remaining_count' => $rows_remaining,
        'row_count' => $row_count,
        'insert_count' => $insert_count,
        'insert_percent' => $insert_percent,
        'inserted_rows' => $inserted_rows,
        'error_messages' => $error_messages,
        'limit' => $limit,
        'new_offset' => ($limit + $offset)
    ));
?>
