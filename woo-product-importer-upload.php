<div class="woo_product_importer_wrapper wrap">
    <h2>Woo Product Importer &raquo; Upload</h2>
    
    <form enctype="multipart/form-data" method="post" action="<?php echo get_admin_url().'tools.php?page=woo-product-importer&action=preview'; ?>">
        <p>
            <label for="import_csv">File to Import</label>
            <input type="file" name="import_csv">
        </p>
        <p>
            <input type="checkbox" name="header_row" id="header_row" value="1">
            <label for="header_row">First Row is Header Row</label>
        </p>
        <p>
            <button class="button-primary" type="submit">Upload and Preview</button>
        </p>
    </form>
</div>