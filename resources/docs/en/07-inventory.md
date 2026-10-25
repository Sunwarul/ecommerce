# Inventory: warehouses and stock movements

## How stock is counted

Stock is kept **per product (or variation) per warehouse**. A branch sells from the warehouse chosen when the POS session is opened.

Stock changes automatically when:

| Event | Effect on stock |
|---|---|
| A sale is **completed** (POS or completing a draft) | Reduced in the session's warehouse |
| A **draft** is saved | No change |
| A completed sale is **voided** | Returned to the order's warehouse |
| An order is moved to **trash** or deleted permanently | **No change** – void it first if the goods came back |

## Stock movement

Open a product → **Stock movement** (permission *Product Movement*). For a product with variations first pick the variation. Choose the **Movement type**:

- **Stock in** – adds to **To warehouse**. Use it for new purchases from a supplier and customer returns.
- **Stock out** – removes from **From warehouse**. Use it for damaged goods, own use or gifts. It refuses if there is not enough stock.
- **Transfer** – moves from one warehouse to another (they must differ).
- **Set quantity** – sets the warehouse quantity to the number you counted (stock-taking). 0 is allowed.

Optional **Branch**, **Reference** (e.g. supplier invoice `PO-102`) and **Note**. Use **Save & add another** for several entries, or **Save & back to product**.

Every movement is recorded with type, quantity, warehouses, reference, note and the user.

**Example – receiving goods:** 20 ACs arrive from Walton with challan `CH-5521`: *Stock in*, To warehouse *Godown*, quantity 20, reference `CH-5521`. Next day 5 go to the showroom: *Transfer*, from *Godown* to *Showroom*, quantity 5.

## Quick stock changes

- **Quick edit stock** (product list) and the pencil on a POS product card (**Quick stock adjust**) set a new quantity per warehouse directly. Use them for quick corrections; prefer **Stock movement** for normal work because it keeps a reference and note.

## Low stock

**Alert Quantity** is set per warehouse on the product. The product list marks low items and the **Stock status → Low stock** filter lists them. The dashboard shows items with fewer than 10 in stock.

## Good practice

- Do a **stock count** monthly: compare the shelf with the product's stock per warehouse and correct with **Set quantity**, reference `COUNT-2026-10`.
- Always enter the supplier's challan/invoice number as the **Reference** for Stock in.
- Never trash an order to "return" goods – **Void** it so stock comes back.
