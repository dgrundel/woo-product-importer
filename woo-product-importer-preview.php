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
    setlocale(LC_ALL, $_POST['user_locale']);

    //get separator options
    $import_csv_hierarchy_separator = isset($_POST['import_csv_hierarchy_separator']) && strlen($_POST['import_csv_hierarchy_separator']) == 1 ? $_POST['import_csv_hierarchy_separator'] : '/';
    $import_csv_separator = isset($_POST['import_csv_separator']) && strlen($_POST['import_csv_separator']) == 1 ? $_POST['import_csv_separator'] : ',';

    $error_messages = array();

    if(isset($_POST['import_csv_url']) && strlen($_POST['import_csv_url']) > 0) {

        $file_path = $_POST['import_csv_url'];

    } elseif(isset($_FILES['import_csv']['tmp_name'])) {

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
            $error_messages[] = sprintf( __( 'Could not create upload directory %s.', 'woo-product-importer' ), $upload_dir );
        }

        //gets uploaded file extension for security check.
        $uploaded_file_ext = strtolower(pathinfo($_FILES['import_csv']['name'], PATHINFO_EXTENSION));

        //full path to uploaded file. slugifys the file name in case there are weird characters present.
        $uploaded_file_path = $upload_dir.'/'.sanitize_title(basename($_FILES['import_csv']['name'],'.'.$uploaded_file_ext)).'.'.$uploaded_file_ext;

        if($uploaded_file_ext != 'csv') {
            $error_messages[] = sprintf( __( 'The file extension %s is not allowed.', 'woo-product-importer' ), $uploaded_file_ext );

        } else {

            if(move_uploaded_file($_FILES['import_csv']['tmp_name'], $uploaded_file_path)) {
                $file_path = $uploaded_file_path;

            } else {
                $error_messages[] = sprintf( __( '%s returned false.', 'woo-product-importer' ), '<code>' . move_uploaded_file() . '</code>' );
            }
        }
    }

    if($file_path) {
        //now that we have the file, grab contents
        $handle = fopen($file_path, 'r' );
        $import_data = array();

        if ( $handle !== FALSE ) {
        	while ( ( $line = fgetcsv($handle, 0, $import_csv_separator) ) !== FALSE ) {
                $import_data[] = $line;
            }
            fclose( $handle );

        } else {
            $error_messages[] = __( 'Could not open file.', 'woo-product-importer' );
        }

        if(intval($_POST['header_row']) == 1 && sizeof($import_data) > 0)
            $header_row = array_shift($import_data);

        $row_count = sizeof($import_data);
        if($row_count == 0)
            $error_messages[] = __( 'No data to import.', 'woo-product-importer' );

    }

    //'mapping_hints' should be all lower case
    //(a strtolower is performed on header_row when checking)
    $col_mapping_options = array(

        'do_not_import' => array(
            'label' => __( 'Do Not Import', 'woo-product-importer' ),
            'mapping_hints' => array()),

        'optgroup_general' => array(
            'optgroup' => true,
            'label' => 'General'),

        'post_title' => array(
            'label' => __( 'Name', 'woo-product-importer' ),
            'mapping_hints' => array('title', 'product name')),
        '_sku' => array(
            'label' => __( 'SKU', 'woo-product-importer' ),
            'mapping_hints' => array()),
        'post_content' => array(
            'label' => __( 'Description', 'woo-product-importer' ),
            'mapping_hints' => array('desc', 'content')),
        'post_excerpt' => array(
            'label' => __( 'Short Description', 'woo-product-importer' ),
            'mapping_hints' => array('short desc', 'excerpt')),

        'optgroup_status' => array(
            'optgroup' => true,
            'label' => 'Status and Visibility'),

        'post_status' => array(
            'label' => __( 'Status (Valid: publish/draft/trash/[more in Codex])', 'woo-product-importer' ),
            'mapping_hints' => array('status', 'product status', 'post status')),
        'menu_order' => array(
            'label' => __( 'Menu Order', 'woo-product-importer' ),
            'mapping_hints' => array('menu order')),
        '_visibility' => array(
            'label' => __( 'Visibility (Valid: visible/catalog/search/hidden)', 'woo-product-importer' ),
            'mapping_hints' => array('visibility', 'visible')),
        '_featured' => array(
            'label' => __( 'Featured (Valid: yes/no)', 'woo-product-importer' ),
            'mapping_hints' => array('featured')),
        '_stock' => array(
            'label' => __( 'Stock', 'woo-product-importer' ),
            'mapping_hints' => array('qty', 'quantity')),
        '_stock_status' => array(
            'label' => __( 'Stock Status (Valid: instock/outofstock)', 'woo-product-importer' ),
            'mapping_hints' => array('stock status', 'in stock')),
        '_backorders' => array(
            'label' => __( 'Backorders (Valid: yes/no/notify)', 'woo-product-importer' ),
            'mapping_hints' => array('backorders')),
        '_manage_stock' => array(
            'label' => __( 'Manage Stock (Valid: yes/no)', 'woo-product-importer' ),
            'mapping_hints' => array('manage stock')),
        'comment_status' => array(
            'label' => __( 'Comment/Review Status (Valid: open/closed)', 'woo-product-importer' ),
            'mapping_hints' => array('comment status')),
        'ping_status' => array(
            'label' => __( 'Pingback/Trackback Status (Valid: open/closed)', 'woo-product-importer' ),
            'mapping_hints' => array('ping status', 'pingback status', 'pingbacks', 'trackbacks', 'trackback status')),

        'optgroup_pricing' => array(
            'optgroup' => true,
            'label' => 'Pricing, Tax, and Shipping'),

        '_regular_price' => array(
            'label' => __( 'Regular Price', 'woo-product-importer' ),
            'mapping_hints' => array('price', '_price', 'msrp')),
        '_sale_price' => array(
            'label' => __( 'Sale Price', 'woo-product-importer' ),
            'mapping_hints' => array()),
        '_tax_status' => array(
            'label' => __( 'Tax Status (Valid: taxable/shipping/none)', 'woo-product-importer' ),
            'mapping_hints' => array('tax status', 'taxable')),
        '_tax_class' => array(
            'label' => __( 'Tax Class', 'woo-product-importer' ),
            'mapping_hints' => array()),
        'product_shipping_class_by_id' => array(
            'label' => __( 'Shipping Class By ID (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array()),
        'product_shipping_class_by_name' => array(
            'label' => __( 'Shipping Class By Name (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array('product_shipping_class', 'shipping_class', 'product shipping class', 'shipping class')),
        '_weight' => array(
            'label' => __( 'Weight', 'woo-product-importer' ),
            'mapping_hints' => array('wt')),
        '_length' => array(
            'label' => __( 'Length', 'woo-product-importer' ),
            'mapping_hints' => array('l')),
        '_width' => array(
            'label' => __( 'Width', 'woo-product-importer' ),
            'mapping_hints' => array('w')),
        '_height' => array(
            'label' => __( 'Height', 'woo-product-importer' ),
            'mapping_hints' => array('h')),

        'optgroup_product_types' => array(
            'optgroup' => true,
            'label' => 'Special Product Types'),

        '_downloadable' => array(
            'label' => __( 'Downloadable (Valid: yes/no)', 'woo-product-importer' ),
            'mapping_hints' => array('downloadable')),
        '_virtual' => array(
            'label' => __( 'Virtual (Valid: yes/no)', 'woo-product-importer' ),
            'mapping_hints' => array('virtual')),
        '_product_type' => array(
            'label' => __( 'Product Type (Valid: simple/variable/grouped/external)', 'woo-product-importer' ),
            'mapping_hints' => array('product type', 'type')),
        '_button_text' => array(
            'label' => __( 'Button Text (External Product Only)', 'woo-product-importer' ),
            'mapping_hints' => array('button text')),
        '_product_url' => array(
            'label' => __( 'Product URL (External Product Only)', 'woo-product-importer' ),
            'mapping_hints' => array('product url', 'url')),
        '_file_paths' => array(
            'label' => __( 'File Path (Downloadable Product Only)', 'woo-product-importer' ),
            'mapping_hints' => array('file path', 'file', 'file_path', 'file paths')),
        '_download_expiry' => array(
            'label' => __( 'Download Expiration (in Days)', 'woo-product-importer' ),
            'mapping_hints' => array('download expiration', 'download expiry')),
        '_download_limit' => array(
            'label' => __( 'Download Limit (Number of Downloads)', 'woo-product-importer' ),
            'mapping_hints' => array('download limit', 'number of downloads')),

        'optgroup_taxonomies' => array(
            'optgroup' => true,
            'label' => 'Categories and Tags'),

        'product_cat_by_name' => array(
            'label' => __( 'Categories By Name (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array('category', 'categories', 'product category', 'product categories', 'product_cat')),
        'product_cat_by_id' => array(
            'label' => __( 'Categories By ID (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array()),
        'product_tag_by_name' => array(
            'label' => __( 'Tags By Name (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array('tag', 'tags', 'product tag', 'product tags', 'product_tag')),
        'product_tag_by_id' => array(
            'label' => __( 'Tags By ID (Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array()),

        'optgroup_custom' => array(
            'optgroup' => true,
            'label' => 'Custom Attributes and Post Meta'),

        'custom_field' => array(
            'label' => __( 'Custom Field / Product Attribute (Set Name Below)', 'woo-product-importer' ),
            'mapping_hints' => array('custom field', 'custom')),
        'post_meta' => array(
            'label' => __( 'Post Meta', 'woo-product-importer' ),
            'mapping_hints' => array('postmeta')),

        'optgroup_images' => array(
            'optgroup' => true,
            'label' => 'Product Images'),

        'product_image_by_url' => array(
            'label' => __( 'Images (By URL, Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array('image', 'images', 'image url', 'image urls', 'product image url', 'product image urls', 'product images')),
        'product_image_by_path' => array(
            'label' => __( 'Images (By Local File Path, Separated by "|")', 'woo-product-importer' ),
            'mapping_hints' => array('image path', 'image paths', 'product image path', 'product image paths'))
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

            if($(this).val() == 'product_image_by_url' || $(this).val() == 'product_image_by_path') {
                $(this).closest('th').find('.product_image_settings').show(400);
            } else {
                $(this).closest('th').find('.product_image_settings').hide(400);
            }

            if($(this).val() == 'post_meta') {
                $(this).closest('th').find('.post_meta_settings').show(400);
            } else {
                $(this).closest('th').find('.post_meta_settings').hide(400);
            }
        });

        //to show the appropriate settings boxes.
        $("select.map_to").trigger('change');

        $(window).resize(function(){
            $("#import_data_preview").addClass("fixed").removeClass("super_wide");
            $("#import_data_preview").css("width", "100%");

            var cell_width = $("#import_data_preview tbody tr:first td:last").width();
            if(cell_width < 60) {
                $("#import_data_preview").removeClass("fixed").addClass("super_wide");
                $("#import_data_preview").css("width", "auto");
            }
        });

        //set table layout
        $(window).trigger('resize');
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Woo Product Importer &raquo; Preview', 'woo-product-importer' ); ?></h2>

    <?php if(sizeof($error_messages) > 0): ?>
        <ul class="import_error_messages">
            <?php foreach($error_messages as $message):?>
                <li><?php echo $message; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if($row_count > 0): ?>
        <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'tools.php?page=woo-product-importer&action=result'; ?>">
            <input type="hidden" name="uploaded_file_path" value="<?php echo htmlspecialchars($file_path); ?>">
            <input type="hidden" name="import_csv_separator" value="<?php echo htmlspecialchars($import_csv_separator); ?>">
            <input type="hidden" name="import_csv_hierarchy_separator" value="<?php echo htmlspecialchars($import_csv_hierarchy_separator); ?>">
            <input type="hidden" name="header_row" value="<?php echo $_POST['header_row']; ?>">
            <input type="hidden" name="user_locale" value="<?php echo htmlspecialchars($_POST['user_locale']); ?>">
            <input type="hidden" name="row_count" value="<?php echo $row_count; ?>">
            <input type="hidden" name="limit" value="5">

            <p>
                <button class="button-primary" type="submit"><?php _e( 'Import', 'woo-product-importer' ); ?></button>
            </p>

            <table id="import_data_preview" class="wp-list-table widefat fixed pages" cellspacing="0">
                <thead>
                    <?php if(intval($_POST['header_row']) == 1): ?>
                        <tr class="header_row">
                            <th colspan="<?php echo sizeof($header_row); ?>"><?php _e( 'CSV Header Row', 'woo-product-importer' ); ?></th>
                        </tr>
                        <tr class="header_row">
                            <?php foreach($header_row as $col): ?>
                                <th><?php echo htmlspecialchars($col); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <?php
                            reset($import_data);
                            $first_row = current($import_data);
                            foreach($first_row as $key => $col):
                        ?>
                            <th>
                                <div class="map_to_settings">
                                    <?php _e( 'Map to:', 'woo-product-importer' ); ?> <select name="map_to[<?php echo $key; ?>]" class="map_to">
                                        <optgroup>
                                        <?php foreach($col_mapping_options as $value => $meta): ?>
                                            <?php if(array_key_exists('optgroup', $meta) && $meta['optgroup'] === true): ?>
                                                </optgroup>
                                                <optgroup label="<?php echo $meta['label']; ?>">
                                            <?php else: ?>
                                                <option value="<?php echo $value; ?>" <?php
                                                    if(intval($_POST['header_row']) == 1) {
                                                        //pre-select this value if the header_row
                                                        //matches the label, value, or any of the hints.
                                                        $header_value = strtolower($header_row[$key]);
                                                        if( $header_value == strtolower($value) ||
                                                            $header_value == strtolower($meta['label']) ||
                                                            in_array($header_value, $meta['mapping_hints']) ) {

                                                            echo 'selected="selected"';
                                                        }
                                                    }
                                                ?>><?php echo $meta['label']; ?></option>
                                            <?php endif;?>
                                        <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="custom_field_settings field_settings">
                                    <h4><?php _e( 'Custom Field Settings', 'woo-product-importer' ); ?></h4>
                                    <p>
                                        <label for="custom_field_name_<?php echo $key; ?>"><?php _e( 'Name', 'woo-product-importer' ); ?></label>
                                        <input type="text" name="custom_field_name[<?php echo $key; ?>]" id="custom_field_name_<?php echo $key; ?>" value="<?php echo $header_row[$key]; ?>" />
                                    </p>
                                    <p>
                                        <input type="checkbox" name="custom_field_visible[<?php echo $key; ?>]" id="custom_field_visible_<?php echo $key; ?>" value="1" checked="checked" />
                                        <label for="custom_field_visible_<?php echo $key; ?>"><?php _e( 'Visible?', 'woo-product-importer' ); ?></label>
                                    </p>
                                </div>
                                <div class="product_image_settings field_settings">
                                    <h4><?php _e( 'Image Settings', 'woo-product-importer' ); ?></h4>
                                    <p>
                                        <input type="checkbox" name="product_image_set_featured[<?php echo $key; ?>]" id="product_image_set_featured_<?php echo $key; ?>" value="1" checked="checked" />
                                        <label for="product_image_set_featured_<?php echo $key; ?>"><?php _e( 'Set First Image as Featured', 'woo-product-importer' ); ?></label>
                                    </p>
                                    <p>
                                        <input type="checkbox" name="product_image_skip_duplicates[<?php echo $key; ?>]" id="product_image_skip_duplicates_<?php echo $key; ?>" value="1" checked="checked" />
                                        <label for="product_image_skip_duplicates_<?php echo $key; ?>"><?php _e( 'Skip Duplicate Images', 'woo-product-importer' ); ?></label>
                                    </p>
                                </div>
                                <div class="post_meta_settings field_settings">
                                    <h4><?php _e( 'Post Meta Settings', 'woo-product-importer' ); ?></h4>
                                    <p>
                                        <label for="post_meta_key_<?php echo $key; ?>"><?php _e( 'Meta Key', 'woo-product-importer' ); ?></label>
                                        <input type="text" name="post_meta_key[<?php echo $key; ?>]" id="post_meta_key_<?php echo $key; ?>" value="<?php echo $header_row[$key]; ?>" />
                                    </p>
                                </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($import_data as $row_id => $row): ?>
                        <tr>
                            <?php foreach($row as $col): ?>
                                <td><?php echo htmlspecialchars($col); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</div>