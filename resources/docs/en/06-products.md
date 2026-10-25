# Products and variations

Open **Catalog → Products**.

## The product list

- **Search** by name, SKU, barcode or product code.
- **Filters**: category, brand, status, product type, warehouse, stock range, price range and **Stock status** (*In stock*, *Low stock*, *Out of stock*).
- **Sort**: newest, oldest, name, price, stock.
- Row actions: **View**, **Edit**, **Stock movement**, **Quick edit stock**, **Move to trash**.
- **Export** downloads the list to Excel.

## Adding a simple product

Click **Add Product**.

1. **Basic information** – **Name** (the slug/web name is made automatically), **Product type: Simple product**, and **Active – visible at the POS**.
2. **Codes** – **SKU**, **Barcode**, **Product Code**. All optional but must be unique. Scan the box barcode into the Barcode field so the POS scanner finds it.
3. **Pricing** – **Selling price** (required). **Discount price** (optional, must be lower) – when set, the POS sells at the discount price.
4. **Category, brand and tags** – **Category** is required; Brand, Tax and Tags are optional.
5. **Product image** – one picture (JPG, PNG or WEBP, up to 7 MB).
6. **Warehouse stock** – one row per warehouse with **Quantity** (opening stock) and **Alert Quantity**. At least one warehouse row is required.
7. Optional: **Physical properties** (weight, dimensions, materials), **Description**, **Additional information**, **Search engine (SEO)**.
8. **Save**.

**Example:** *Walton 1.5 Ton Inverter AC* – SKU `WAL-AC-15T`, barcode from the box, selling price 72,500, discount price 69,900, category *Inverter AC*, brand *Walton*, warehouse *Showroom* quantity 5, alert 2; warehouse *Godown* quantity 20, alert 5.

> **Important:** the stock numbers on the product form are for **opening stock**. Later changes should be made with **Stock movement** so there is a history. Saving the edit form of a simple product replaces the warehouse quantities with the numbers shown in the form – if sales happened while the form was open, re-check stock after saving.

## Adding a product with variations

Use this when one product is sold in several versions (colour, size, capacity…). Create the **Attributes** and **Attribute Values** first (see *Catalog setup*).

1. **Product type: Product with variations**.
2. Fill name, category, brand, selling price as above. The selling price is the default price of each variation.
3. In **Build variations from attributes**, open **Select values** for each attribute and tick the values you sell, e.g. *Colour: White, Brown* and *Size: 48", 56"*.
4. Click **Generate variations**. Every combination is created (2 colours × 2 sizes = 4 variations) with an automatic SKU such as `CEILING-FAN-WHITE-56`.
5. Adjust each variation's **SKU**, **Price**, **Discount price** and **Low-stock alert per warehouse**. Use **Add one manually** for a single extra combination or **Remove all** to start again.
6. **Save**.
7. **Add stock:** variations start with 0 stock. For each variation open **Stock movement → Stock in** and enter the quantity per warehouse.

Regenerating later keeps the SKU and prices of combinations that already exist. Removing a variation deletes its stock.

At the POS, a product with variations opens **Select variation**, showing the stock of each variation by warehouse. Scanning a **variation SKU** adds that variation directly.

## Viewing a product

**View** shows prices, codes, every variation and the stock per warehouse with low-stock rows highlighted.

## Deleting products

- **Move to trash** hides the product; **Restore** brings it back.
- **Delete permanently** removes it with its variations and stock. Products that appear on sales invoices are kept to protect your history.
