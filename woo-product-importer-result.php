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

    $post_data = array(
        'uploaded_file_path' => $_POST['uploaded_file_path'],
        'header_row' => $_POST['header_row'],
        'limit' => intval($_POST['limit']),
        'map_to' => $_POST['map_to'],
        'custom_field_name' => $_POST['custom_field_name'],
        'custom_field_visible' => $_POST['custom_field_visible'],
        'product_image_set_featured' => $_POST['product_image_set_featured'],
        'product_image_skip_duplicates' => $_POST['product_image_skip_duplicates'],
        'post_meta_key' => $_POST['post_meta_key'],
        'user_locale' => $_POST['user_locale'],
        'import_csv_separator' => $_POST['import_csv_separator'],
        'import_csv_hierarchy_separator' => $_POST['import_csv_hierarchy_separator']
    );
?>
<script type="text/javascript">
    jQuery(document).ready(function($){

        $("#show_debug").click(function(){
            $("#debug").show();
            $(this).hide();
        });

        doAjaxImport(<?php echo $post_data['limit']; ?>, 0);

        function doAjaxImport(limit, offset) {
            var data = {
                "action": "woo-product-importer-ajax",
                "uploaded_file_path": <?php echo json_encode($post_data['uploaded_file_path']); ?>,
                "header_row": <?php echo json_encode($post_data['header_row']); ?>,
                "limit": limit,
                "offset": offset,
                "map_to": '<?php echo (serialize($post_data['map_to'])); ?>',
                "custom_field_name": '<?php echo (serialize($post_data['custom_field_name'])); ?>',
                "custom_field_visible": '<?php echo (serialize($post_data['custom_field_visible'])); ?>',
                "product_image_set_featured": '<?php echo (serialize($post_data['product_image_set_featured'])); ?>',
                "product_image_skip_duplicates": '<?php echo (serialize($post_data['product_image_skip_duplicates'])); ?>',
                "post_meta_key": '<?php echo (serialize($post_data['post_meta_key'])); ?>',
                "user_locale": '<?php echo (serialize($post_data['user_locale'])); ?>',
                "import_csv_separator": '<?php echo (serialize($post_data['import_csv_separator'])); ?>',
                "import_csv_hierarchy_separator": '<?php echo (serialize($post_data['import_csv_hierarchy_separator'])); ?>'
            };

            //ajaxurl is defined by WordPress
            $.post(ajaxurl, data, ajaxImportCallback);
        }

        function ajaxImportCallback(response_text) {

            $("#debug").append($(document.createElement("p")).text(response_text));

            var response = jQuery.parseJSON(response_text);

            $("#insert_count").text(response.insert_count + " (" + response.insert_percent +"%)");
            $("#remaining_count").text(response.remaining_count);
            $("#row_count").text(response.row_count);

            //show inserted rows
            for(var row_num in response.inserted_rows) {
                var tr = $(document.createElement("tr"));

                if(response.inserted_rows[row_num]['success'] == true) {
                    if(response.inserted_rows[row_num]['has_errors'] == true) {
                        tr.addClass("error");
                    } else {
                        tr.addClass("success");
                    }
                } else {
                    tr.addClass("fail");
                }

                var post_link = $(document.createElement("a"));
                post_link.attr("target", "_blank");
                post_link.attr("href", "<?php echo get_admin_url(); ?>post.php?post=" + response.inserted_rows[row_num]['post_id'] + "&action=edit");
                post_link.text(response.inserted_rows[row_num]['post_id']);

                tr.append($(document.createElement("td")).append($(document.createElement("span")).addClass("icon")));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['row_id']));
                tr.append($(document.createElement("td")).append(post_link));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['name']));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['sku']));
                tr.append($(document.createElement("td")).text(response.inserted_rows[row_num]['price']));

                var result_messages = "";
                if(response.inserted_rows[row_num]['has_messages'] == true) {
                    result_messages += response.inserted_rows[row_num]['messages'].join("\n") + "\n";
                }
                if(response.inserted_rows[row_num]['has_errors'] == true) {
                    result_messages += response.inserted_rows[row_num]['errors'].join("\n") + "\n";
                } else {
                    result_messages += "No errors.";
                }
                tr.append($(document.createElement("td")).text(result_messages));

                tr.appendTo("#inserted_rows tbody");
            }

            //show error messages
            for(var message in response.error_messages) {
                $(document.createElement("li")).text(response.error_messages[message]).appendTo(".import_error_messages");
            }

            //move on to the next set!
            if(parseInt(response.remaining_count) > 0) {
                doAjaxImport(response.limit, response.new_offset);
            } else {
                $("#import_status").addClass("complete");
            }
        }
    });
</script>

<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2><?php _e( 'Woo Product Importer &raquo; Results', 'woo-product-importer' ); ?></h2>

    <ul class="import_error_messages">
    </ul>

    <div id="import_status">
        <div id="import_in_progress">
            <img src="<?php echo plugin_dir_url(__FILE__); ?>img/ajax-loader.gif"
                alt="<?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'woo-product-importer' ); ?>"
                title="<?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'woo-product-importer' ); ?>">

            <strong><?php _e( 'Importing. Please do not close this window or click your browser\'s stop button.', 'woo-product-importer' ); ?></strong>
        </div>
        <div id="import_complete">
            <img src="<?php echo plugin_dir_url(__FILE__); ?>img/complete.png"
                alt="<?php _e( 'Import complete!', 'woo-product-importer' ); ?>"
                title="<?php _e( 'Import complete!', 'woo-product-importer' ); ?>">
            <strong><?php _e( 'Import Complete! Results below.', 'woo-product-importer' ); ?></strong>
        </div>

        <table>
            <tbody>
                <tr>
                    <th><?php _e( 'Processed', 'woo-product-importer' ); ?></th>
                    <td id="insert_count">0</td>
                </tr>
                <tr>
                    <th><?php _e( 'Remainin', 'woo-product-importer' ); ?>g</th>
                    <td id="remaining_count"><?php echo $post_data['row_count']; ?></td>
                </tr>
                <tr>
                    <th><?php _e( 'Total', 'woo-product-importer' ); ?></th>
                    <td id="row_count"><?php echo $post_data['row_count']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <table id="inserted_rows" class="wp-list-table widefat fixed pages" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 30px;"></th>
                <th style="width: 80px;"><?php _e( 'CSV Row', 'woo-product-importer' ); ?></th>
                <th style="width: 80px;"><?php _e( 'New Post ID', 'woo-product-importer' ); ?></th>
                <th><?php _e( 'Name', 'woo-product-importer' ); ?></th>
                <th><?php _e( 'SKU', 'woo-product-importer' ); ?></th>
                <th style="width: 120px;"><?php _e( 'Price', 'woo-product-importer' ); ?></th>
                <th><?php _e( 'Result', 'woo-product-importer' ); ?></th>
            </tr>
        </thead>
        <tbody><!-- rows inserted via AJAX --></tbody>
    </table>

    <p><a id="show_debug" href="#" class="button"><?php _e( 'Show Raw AJAX Responses', 'woo-product-importer' ); ?></a></p>
    <div id="debug"><!-- server responses get logged here --></div>

    <div id="credits">
        <div id="donate_form">
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="YX9JSKX6778HQ">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="<?php _e( 'PayPal - The safer, easier way to pay online!', 'woo-product-importer' ); ?>">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
        </div>

        <p><?php _e( 'If you found this plugin useful, and it saved you some time, money, or frustration, consider making a donation. Any amount is helpful!', 'woo-product-importer' ); ?></p>
        <p><?php _e( 'If you\'re having a problem with the plugin, <a href="https://github.com/dgrundel/woo-product-importer/issues/new" target="_blank">post your issue here</a> and we\'ll do our best to help.', 'woo-product-importer' ); ?></p>
        <p>
            <?php _e( 'Woo Product Importer was created by Daniel Grundel of <a href="http://webpresencepartners.com" target="_blank">Web Presence Partners</a> and is copyright 2012-2013 Web Presence Partners LLC.
            It is licensed under the GNU LGPL v3.
            <em>ajax-loader.gif</em> courtesy of <a href="http://ajaxload.info" target="_blank">ajaxload.info</a>.
            The <em>gigantic checkmark</em> is public domain.
            All other icons are from the <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">Silk icon set</a> by Mark James.', 'woo-product-importer' ); ?>
        </p>
        <div style="clear:both"></div>
    </div>
</div>