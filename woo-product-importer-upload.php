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
*/ ?>
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
    
    <table class="form-table">
        <tbody>
            <tr>
                <th>Path to Your <strong>uploads</strong> Folder</th>
                <td><?php
                    $upload_dir = wp_upload_dir();
                    echo $upload_dir['basedir'];
                ?></td>
            </tr>
        </tbody>
    </table>
</div>