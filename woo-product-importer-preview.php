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
        $uploaded_file_path = $upload_dir.'/'.WebPres_Woo_Product_Importer::generate_slug(basename($_FILES['import_csv']['name'],'.'.$uploaded_file_ext)).'.'.$uploaded_file_ext;
        
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
            $error_messages[] = 'No data imported.';
        }
    }
    
    $col_mapping_options = array(
        'do_not_import' => 'Do Not Import',
        'post_title' => 'Name',
        'post_content' => 'Description',
        '_price' => 'Price',
        '_sale_price' => 'Sale Price',
        '_tax_status' => 'Tax Status',
        '_tax_class' => 'Tax Class',
        '_visibility' => 'Visibility',
        '_featured' => 'Featured',
        '_weight' => 'Weight',
        '_length' => 'Length',
        '_width' => 'Width',
        '_height' => 'Height',
        '_sku' => 'SKU',
        '_downloadable' => 'Downloadable',
        '_virtual' => 'Virtual',
        '_stock' => 'Stock',
        '_stock_status' => 'Stock Status',
        '_backorders' => 'Backorders',
        '_manage_stock' => 'Manage Stock'
    );
    
?>
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
        
        <p>
            <button class="button-primary" type="submit">Import</button>
        </p>
        
        <table class="wp-list-table widefat fixed pages" cellspacing="0">
            <thead>
                <?php if($_POST['header_row'] == '1'):
                    $header_row = array_shift($import_data); ?>
                    <tr>
                        <th>CSV Header Row</th>
                        <?php foreach($header_row as $col): ?>
                            <th><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th>Import?</th>
                    <?php
                        reset($import_data);
                        $first_row = current($import_data);
                        foreach($first_row as $key => $col):
                    ?>
                        <th>
                            Map to: <select name="map_to[<?php echo $key; ?>]">
                                <?php foreach($col_mapping_options as $value => $name): ?>
                                    <option value="<?php echo $value; ?>" <?php if($header_row[$key] == $value || $header_row[$key] == $name) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($import_data as $row_id => $row): ?>
                    <tr>
                        <th><input type="checkbox" name="import_row[<?php echo $row_id; ?>]" value="1" checked="checked" /></th>
                        <?php foreach($row as $col): ?>
                            <td><?php echo htmlspecialchars($col); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p>
            <button class="button-primary" type="submit">Import</button>
        </p>
    </form>
</div>