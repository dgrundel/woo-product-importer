<?php
    if(isset($_FILES['import_csv']['tmp_name'])) {
        
        $error_messages = array();
        
        if(function_exists('wp_upload_dir')) {
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'].'/csv_import';
        } else {
            $upload_dir = dirname(__FILE__).'/uploads';
        }
        
        if(!file_exists($upload_dir)) {
            $old_umask = umask(0);
            mkdir($upload_dir, 0755, true);
            umask($old_umask);
        }
        if(!file_exists($upload_dir)) {
            $error_messages[] = 'Could not create upload directory "'.$upload_dir.'".';
        }
        
        //gets uploaded file extension for security check.
        $uploaded_file_ext = strtolower(pathinfo($_FILES['import_csv']['name'], PATHINFO_EXTENSION));
        
        //full path to uploaded file. slugifys the file name in case there are weird characters present.
        $uploaded_file_path = $upload_dir.'/'.sanitize_title(basename($_FILES['import_csv']['name'],'.'.$uploaded_file_ext)).'.'.$uploaded_file_ext;
        
        if($uploaded_file_ext != 'csv') {
            $error_messages[] = 'The file extension "'.$uploaded_file_ext.'" is not allowed.';
            
        } else {
            
            if(move_uploaded_file($_FILES['import_csv']['tmp_name'], $uploaded_file_path)) {
                
                //now that we have the file, grab contents
                $handle = fopen( $uploaded_file_path, 'r' );
                $import_data = array();
                
                if ( $handle !== FALSE ) {
                    while ( ( $line = fgetcsv($handle) ) !== FALSE ) {
                        $import_data[] = $line;
                    }
                    fclose( $handle );
                    
                } else {
                    $error_messages[] = 'Could not open file.';
                }
                
            } else {
                 $error_messages[] = 'move_uploaded_file() returned false.';
            }
        }
        
        if(sizeof($import_data) == 0) {
            $error_messages[] = 'No data to import.';
        }
        
        if(intval($_POST['header_row']) == 1)
            $header_row = array_shift($import_data);
            
        $row_count = sizeof($import_data);
    }
    
    $col_mapping_options = array(
        'do_not_import' => 'Do Not Import',
        'post_title' => 'Name',
        'post_content' => 'Description',
        'post_excerpt' => 'Short Description',
        '_price' => 'Price',
        '_sale_price' => 'Sale Price',
        '_tax_status' => 'Tax Status (Valid: taxable/shipping/none)',
        '_tax_class' => 'Tax Class',
        '_visibility' => 'Visibility (Valid: visible/catalog/search/hidden)',
        '_featured' => 'Featured (Valid: yes/no)',
        '_weight' => 'Weight',
        '_length' => 'Length',
        '_width' => 'Width',
        '_height' => 'Height',
        '_sku' => 'SKU',
        '_downloadable' => 'Downloadable (Valid: yes/no)',
        '_virtual' => 'Virtual (Valid: yes/no)',
        '_stock' => 'Stock',
        '_stock_status' => 'Stock Status (Valid: instock/outofstock)',
        '_backorders' => 'Backorders (Valid: yes/no/notify)',
        '_manage_stock' => 'Manage Stock (Valid: yes/no)',
        '_product_type' => 'Product Type (Valid: simple/variable/grouped/external)',
        '_product_url' => 'Product URL',
        'product_cat_by_name' => 'Categories By Name (Separated by "|")',
        'product_cat_by_id' => 'Categories By ID (Separated by "|")',
        'product_tag_by_name' => 'Tags By Name (Separated by "|")',
        'product_tag_by_id' => 'Tags By ID (Separated by "|")',
        'custom_field' => 'Custom Field (Set Name Below)',
        'product_image' => 'Images (By URL, Separated by "|")'
    );
    
?>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $("select.map_to").change(function(){
            
            if($(this).val() == 'custom_field') {
                $(this).closest('th').find('.custom_field_settings').show(400);
            } else {
                $(this).closest('th').find('.custom_field_settings').hide(400);
            }
            
            if($(this).val() == 'product_image') {
                $(this).closest('th').find('.product_image_settings').show(400);
            } else {
                $(this).closest('th').find('.product_image_settings').hide(400);
            }
        });
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2>Woo Product Importer &raquo; Preview</h2>
    
    <?php if(sizeof($error_messages) > 0): ?>
        <ul class="import_error_messages">
            <?php foreach($error_messages as $message):?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'tools.php?page=woo-product-importer&action=result'; ?>">
        <input type="hidden" name="uploaded_file_path" value="<?php echo htmlspecialchars($uploaded_file_path); ?>">
        <input type="hidden" name="header_row" value="<?php echo $_POST['header_row']; ?>">
        <input type="hidden" name="row_count" value="<?php echo $row_count; ?>">
        <input type="hidden" name="limit" value="5">
        
        <p>
            <button class="button-primary" type="submit">Import</button>
        </p>
        
        <table class="wp-list-table widefat fixed pages" cellspacing="0">
            <thead>
                <?php if(intval($_POST['header_row']) == 1): ?>
                    <tr class="header_row">
                        <th class="narrow">CSV Header Row</th>
                        <?php foreach($header_row as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
                <tr>
                    <?php if($row_count < 100): ?><th class="narrow">Import?</th><?php endif; ?>
                    <?php
                        reset($import_data);
                        $first_row = current($import_data);
                        foreach($first_row as $key => $col):
                    ?>
                        <th>
                            <div class="map_to_settings">
                                Map to: <select name="map_to[<?php echo $key; ?>]" class="map_to">
                                    <?php foreach($col_mapping_options as $value => $name): ?>
                                        <option value="<?php echo $value; ?>" <?php if($header_row[$key] == $value || $header_row[$key] == $name) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="custom_field_settings field_settings">
                                <h4>Custom Field Settings</h4>
                                <p>
                                    <label for="custom_field_name_<?php echo $key; ?>">Name</label>
                                    <input type="text" name="custom_field_name[<?php echo $key; ?>]" id="custom_field_name_<?php echo $key; ?>" value="<?php echo $header_row[$key]; ?>" />
                                </p>
                                <p>
                                    <input type="checkbox" name="custom_field_visible[<?php echo $key; ?>]" id="custom_field_visible_<?php echo $key; ?>" value="1" checked="checked" />
                                    <label for="custom_field_visible_<?php echo $key; ?>">Visible?</label>
                                </p>
                            </div>
                            <div class="product_image_settings field_settings">
                                <h4>Image Settings</h4>
                                <p>
                                    <input type="checkbox" name="product_image_set_featured[<?php echo $key; ?>]" id="product_image_set_featured_<?php echo $key; ?>" value="1" checked="checked" />
                                    <label for="product_image_set_featured_<?php echo $key; ?>">Set First Image as Featured</label>
                                </p>
                            </div>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($import_data as $row_id => $row): ?>
                    <tr>
                        <?php if($row_count < 100): ?><td class="narrow"><input type="checkbox" name="import_row[<?php echo $row_id; ?>]" value="1" checked="checked" /></td><?php endif; ?>
                        <?php foreach($row as $col): ?>
                            <td><?php echo htmlspecialchars($col); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>