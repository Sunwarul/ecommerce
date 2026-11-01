# Catalog setup: categories, brands, attributes and more

All of these are under **Catalog → Product setup**. Every list works the same way:

- **New** opens the form; **Save & add another** keeps it open for fast entry.
- Click a row's edit icon to change it.
- **Delete** moves a record to **Trash**. Switch the list to **Trash** to **Restore** it or **Delete permanently**.
- Tick several rows for bulk delete / restore.
- **Export** downloads an Excel file (available for categories, sub-categories, brands, tags and products).

## Categories

Fields: **Name** (required, unique), **Parent Category** (leave empty for a main category), photo, active.

Example structure for an electronics showroom:

- Air Conditioner → *Split AC*, *Inverter AC*, *Window AC*
- Refrigerator → *Single door*, *Double door*, *Deep freezer*
- Television → *LED*, *Smart TV*
- Home appliance → *Fan*, *Rice cooker*, *Blender*

> Create categories before products – **category is required on every product**. Warranty templates can also be linked to a category so the POS suggests the right warranty.

## Sub categories

Fields: **Category** (required), **Name**, description, photo. This is a reference list; products are not linked to sub-categories yet. For product grouping use child categories (Parent Category) as shown above.

## Brands

Fields: **Name** (required, unique), description, **Logo**, active. Optional on a product but useful for reports (**Sales by brand**) and filters.

## Tags

Fields: **Name**, active. Use tags for cross-cutting groups such as *Eid offer*, *Best seller*, *Inverter*.

## Attributes and attribute values

Needed only for **products with variations**.

1. **Attributes** – **Key** (internal name, lowercase without spaces, e.g. `colour`), **Display name** (*Colour*), **Input type** (*Dropdown*, *Radio*, *Colour swatch* or *Buttons*), active.
2. **Attribute Values** – choose the **Attribute**, then **Value** (e.g. `white`), optional **Display value** (*White*) and **Colour** (hex, for swatches).

Example: attribute *Capacity* with values *1 Ton*, *1.5 Ton*, *2 Ton*; attribute *Colour* with *White*, *Silver*.

## Units and base units

- **Base Units** – a measurement group, e.g. *Quantity*, *Weight*, *Length*.
- **Units** – **Name**, **Short code** and the **Base unit** it belongs to, e.g. *Piece (pcs)* under *Quantity*, *Kilogram (kg)* under *Weight*.

Units are a reference list today; the POS sells whole quantities.

## Taxes

Fields: **Name**, **Rate type** (*Percentage (%)* or *Fixed amount*), **Rate**. A tax can be chosen on a product. Tax is not yet calculated at the POS, so invoices show tax 0. Deleting a tax is permanent (no Trash).

## Warranty & guarantees

Ready-made warranty texts the cashier can pick at the POS.

Fields: **Name** (e.g. *Compressor warranty*), **Type** (*Warranty*, *Guarantee*, *Service*, *None*), **Duration** (free text, e.g. *10 years compressor, 1 year parts*), **Warranty text** (printed on the invoice), active, and **Category** (or *All categories*). Templates whose category matches the cart are shown first at the POS, marked **Relevant**.
