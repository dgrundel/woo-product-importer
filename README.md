woo-product-importer
====================

A simple, free CSV importer for WooCommerce

**Cool Stuff You Can Do**
- Import Categories and Tags (Categories and Tags are created if they don't exist)
- Import Custom Fields
- Set Visibility on Custom Fields
- Map any CSV column to any Product field (No header row required, but if you happened to include one, I'll use it to take a stab at what goes where)
- Map multiple CSV columns to one Product field (last non-empty column wins, good for merging columns)

More information on our blog:
http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/

Once installed, go to Tools -> Woo Product Importer in the Admin Panel to use.

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
- product_cat (Categories (By Name Separated by "|"))
- product_tag (Tags (By Name Separated by "|"))
- Custom Fields