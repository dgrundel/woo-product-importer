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
        if($_POST['header_row'] == '1') array_shift($import_data);
        
        $inserted_rows = array();
        
        //this is where the fun begins
        foreach($import_data as $row_id => $row) {
            
            //don't import if the checkbox wasn't checked
            if($_POST['import_row'][$row_id] != '1') continue;
            
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
            $new_post_meta['_product_attributes'] = 'a:0:{}';
            $new_post_meta['_downloadable'] = 'no';
            $new_post_meta['_virtual'] = 'no';
            $new_post_meta['_backorders'] = 'no';
            $new_post_meta['_manage_stock'] = 'no';
            
            foreach($row as $key => $col) {
                $map_to = $_POST['map_to'][$key];
                
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
                        $new_post_meta[$map_to] = woo_product_importer_clean_number($col);
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
                }
            }
            
            //set some more post_meta and parse things as appropriate
            $new_post_meta['_regular_price'] = $new_post_meta['_price'];
            
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
                    
                    foreach($new_post_meta as $meta_key => $meta_value) {
                        update_post_meta($new_post_id, $meta_key, $meta_value);
                    }
                }
                
            } else {
                $error_messages[] = 'Skipped import of product without a name';
            }
        }
    }
    
?>
<div class="woo_product_importer_wrapper wrap">
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