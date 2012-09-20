<?php
    if(isset($_POST['uploaded_file_path'])) {
        
        $error_messages = array();
        
        //now that we have the file, grab contents
        $temp_file_path = $_POST['uploaded_file_path'];
        $handle = fopen( $temp_file_path, 'r' );
        $import_data = array();
        
        if ( $handle !== FALSE ) {
            while ( ( $line = fgetcsv($handle) ) !== FALSE ) {
                $import_data[] = $line;
            }
            fclose( $handle );
        } else {
            $error_messages[] = 'Could not open file.';
        }
        
        if(sizeof($import_data) == 0) {
            $error_messages[] = 'No data imported.';
        }
        
        //discard header row
        if(intval($_POST['header_row']) == 1) array_shift($import_data);
        
        $inserted_rows = array();
        
        //this is where the fun begins
        foreach($import_data as $row_id => $row) {
            
            //don't import if the checkbox wasn't checked
            if(intval($_POST['import_row'][$row_id]) != 1) continue;
            
            //$post = array(
            //    'post_content' => [ <the text of the post> ] //The full text of the post.
            //    'post_status' => 'publish' //Set the status of the new post. 
            //    'post_title' => [ <the title> ] //The title of your post.
            //    'post_type' => 'product' //You may want to insert a regular post, page, link, a menu item or some custom post type
            //    'tax_input' => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
            //  );  
            
            //set some initial post values
            $new_post = array();
            $new_post['post_type'] = 'product';
            $new_post['post_status'] = 'publish';
            $new_post['post_title'] = '';
            $new_post['post_content'] = '';
            
            //set some initial post_meta values
            $new_post_meta = array();
            $new_post_meta['_visibility'] = 'visible';
            $new_post_meta['_featured'] = 'no';
            $new_post_meta['_weight'] = 0;
            $new_post_meta['_length'] = 0;
            $new_post_meta['_width'] = 0;
            $new_post_meta['_height'] = 0;
            $new_post_meta['_sku'] = '';
            $new_post_meta['_stock'] = '';
            $new_post_meta['_stock_status'] = 'instock';
            $new_post_meta['_sale_price'] = '';
            $new_post_meta['_sale_price_dates_from'] = '';
            $new_post_meta['_sale_price_dates_to'] = '';
            $new_post_meta['_tax_status'] = 'taxable';
            $new_post_meta['_tax_class'] = '';
            $new_post_meta['_purchase_note'] = '';
            $new_post_meta['_downloadable'] = 'no';
            $new_post_meta['_virtual'] = 'no';
            $new_post_meta['_backorders'] = 'no';
            $new_post_meta['_manage_stock'] = 'no';
            
            //this is a multidimensional array that stores tax and term ids.
            //format is: array( 'tax_name' => array(1, 3, 4), 'another_tax_name' => array(5, 9, 23) )
            $new_post_terms = array();
            
            $new_post_custom_fields = array();
            $new_post_custom_field_count = 0;
            
            foreach($row as $key => $col) {
                $map_to = $_POST['map_to'][$key];
                
                //skip if the column is blank.
                //useful when two CSV cols are mapped to the same product field.
                //you would do this to merge two columns in your CSV into one product field.
                if(strlen($col) == 0) {
                    continue;
                }
                
                switch($map_to) {
                    case 'post_title':
                    case 'post_content':
                        $new_post[$map_to] = $col;
                        break;
                    
                    case '_weight':
                    case '_length':
                    case '_width':
                    case '_height':
                    case '_regular_price':
                    case '_sale_price':
                    case '_price':
                        //remove any non-numeric chars except for '.'
                        $new_post_meta[$map_to] = preg_replace("/[^0-9.]/", "", $col);
                        break;
                    
                    case '_tax_status':
                    case '_tax_class':
                    case '_visibility':
                    case '_featured':
                    case '_sku':
                    case '_downloadable':
                    case '_virtual':
                    case '_stock':
                    case '_stock_status':
                    case '_backorders':
                    case '_manage_stock':
                        $new_post_meta[$map_to] = $col;
                        break;
                    
                    case 'product_cat':
                    case 'product_tag':
                        $term_names = explode('|', $col);
                        foreach($term_names as $term_name) {
                            $term = get_term_by('name', $term_name, $map_to, 'ARRAY_A');
                            
                            //if term does not exist, try to insert it.
                            if($term === false) {
                                $term = wp_insert_term($term_name, $map_to);
                            }
                            
                            //if we got a term, save the id so we can associate
                            if(is_array($term)) {
                                $new_post_terms[$map_to][] = intval($term['term_id']);
                            }
                        }
                        break;
                    
                    case 'custom_field':
                        $field_name = $_POST['custom_field_name'][$key];
                        $field_slug = sanitize_title($field_name);
                        $visible = intval($_POST['custom_field_visible'][$key]);
                        
                        $new_post_custom_fields[$field_slug] = array (
                            "name" => $field_name,
                            "value" => $col,
                            "position" => $new_post_custom_field_count++,
                            "is_visible" => $visible,
                            "is_variation" => 0,
                            "is_taxonomy" => 0
                        );
                        break;
                }
            }
            
            //set some more post_meta and parse things as appropriate
            $new_post_meta['_regular_price'] = $new_post_meta['_price'];
            $new_post_meta['_product_attributes'] = serialize($new_post_custom_fields);
            
            if(strlen($new_post['post_title']) > 0) {
                $new_post_id = wp_insert_post($new_post, true);
                
                if(is_wp_error($new_post_id)) {
                    $error_messages[] = 'Couldn\'t insert product with name "'.$new_post['post_title'].'".';
                } else {
                    //insert successful
                    $inserted_rows[$new_post_id] = array(
                        'new_post' => $new_post,
                        'new_post_meta' => $new_post_meta
                    );
                    
                    //set post_meta on inserted post
                    foreach($new_post_meta as $meta_key => $meta_value) {
                        update_post_meta($new_post_id, $meta_key, $meta_value);
                    }
                    
                    //set post terms on inserted post
                    foreach($new_post_terms as $tax => $term_ids) {
                        wp_set_object_terms($new_post_id, $term_ids, $tax);
                    }
                }
                
            } else {
                $error_messages[] = 'Skipped import of product without a name';
            }
        }
    }
    
?>
<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2>Woo Product Importer &raquo; Results</h2>
    
    <?php if(sizeof($error_messages) > 0): ?>
        <ul class="import_error_messages">
            <?php foreach($error_messages as $message):?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <p>Inserted <?php echo sizeof($inserted_rows); ?> of <?php echo sizeof($import_data); ?> row(s) successfully.</p>
    
    <table class="wp-list-table widefat fixed pages" cellspacing="0">
        <thead>
            <tr>
                <th>Post ID</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inserted_rows as $id => $data): ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $data['new_post']['post_title']; ?></td>
                    <td><?php echo $data['new_post_meta']['_price']; ?></td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>