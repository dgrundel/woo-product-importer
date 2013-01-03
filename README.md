woo-product-importer
====================

A simple, free CSV importer for WooCommerce.

Once installed, go to Tools -> Woo Product Importer in the Admin Panel to use.

![Import Complete Screenshot](http://webpresencepartners.com/wp-content/uploads/2012/10/complete1.png)

A sample CSV file is included to help you get started. (woo-importer-example.csv)

**Cool Stuff It/You Can Do**
- AJAXy Importing (avoids timing out on large data sets)
- Import Images via a URL (Requires allow_url_fopen or cURL on your server)
- Set Featured Image
- Import Categories and Tags (Categories and Tags are created if they don't exist)
- Add Categories and Tags to Products by Category/Tag Name or ID.
- Import Custom Fields
- Set Visibility on Custom Fields
- Map any CSV column to any Product field (No header row required, but if you happened to include one, I'll use it to take a stab at what goes where)
- Map multiple CSV columns to one Product field (last non-empty column wins, good for merging columns)
- Basic validation for multiple-choice fields (fields with a limited set of valid values like yes/no, instock/outofstock, etc.)
- Dollar signs, commas, etc. are stripped out of number fields like prices, weight, length, width, height
- If SKU already exists, existing product is updated rather than inserting new product.

Here's a look at the preview/column mapping screen:
![Column Mapping Screenshot](http://webpresencepartners.com/wp-content/uploads/2012/10/preview.png)

**Importable Product Types**
- Simple Products
- Virtual Products
- Downloadable Products
- External Products

**Full List of Importable Attributes**
- post_title (Name)
- post_content (Description)
- post_excerpt (Short Description)
- _price (Price)
- _sale_price (Sale Price)
- _tax_status (Tax Status)
- _tax_class (Tax Class)
- _visibility (Visibility)
- _featured (Featured)
- _weight (Weight)
- _length (Length)
- _width (Width)
- _height (Height)
- _sku (SKU)
- _downloadable (Downloadable)
- _virtual (Virtual)
- _stock (Stock)
- _stock_status (Stock Status)
- _backorders (Backorders)
- _manage_stock (Manage Stock)
- product_cat (Categories, by Name or ID)
- product_tag (Tags, by Name or ID)
- Custom Fields
- Product Images (By URL or Local File Path)
- _button_text (Button Text, for External Products)
- _product_url (Product URL, for External Products)
- _file_path (File Path, for Downloadable Product)
- _download_expiry (Download Expiration, in Days)
- _download_limit (Download Limit, an integer)

**Not Supported (Yet)**
- Variable and Grouped Products
- Custom taxonomies
- Attributes (and Global Attributes)

If you see something on the 'Not Supported' list that you absolutely must have, let me know. We can try to move it up on the priority list or, (*cough* *cough*) you could hire us!

**Other Items on the To Do List**
- Test and refine handling of existing product. I suspect that certain existing attributes will be overwritten if not explicitly re-set in the CSV.

**Thank Yous**

Thanks to the following folks for helping make this plugin even better!

- Github user becasual for running his server out of memory, prompting me to add AJAXy importing and for finding that spaces in image URLs were causing problems
- Subhan Toba for noting that we were inserting duplicate SKUs, prompting me to support product updating

More information on our blog:
http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/