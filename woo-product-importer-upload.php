<div class="woo_product_importer_wrapper wrap">
    <div id="icon-tools" class="icon32"><br /></div>
    <h2>Woo Product Importer &raquo; Upload</h2>
    
    <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'tools.php?page=woo-product-importer&action=preview'; ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="import_csv">File to Import</label></th>
                    <td><input type="file" name="import_csv"></td>
                </tr>
                <tr>
                    <th><label for="import_csv">URL to Import</label></th>
                    <td>
                        <input type="text" name="import_csv_url" class="regular-text code">
                        <p class="description">Enter the full URL to a CSV file. Leave this field blank if uploading a file.</p>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="checkbox" name="header_row" id="header_row" value="1">
                        <label for="header_row">First Row is Header Row</label>
                    </td>
                <tr>
                    <th></th>
                    <td><button class="button-primary" type="submit">Upload and Preview</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>