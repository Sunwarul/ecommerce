# Key business terms

Read this chapter once – the rest of the manual uses these words.

## Organisation

| Term | Meaning |
|---|---|
| **Branch** | A shop / outlet of your business. Orders, payments, installment agreements and EMI records belong to a branch. Staff switch branch from the top bar. |
| **Warehouse** | A place where stock is kept (showroom floor, back store, godown). Stock is counted **per warehouse**. Any branch can sell from any warehouse; you choose the warehouse when opening a POS session. |
| **User** | A staff member who logs in. |
| **Role** | A named set of permissions, e.g. *Cashier*, *Manager*. Users get one or more roles. |
| **Permission** | Permission to do one thing, e.g. *Product Export* or *Order Void*. |
| **Super Admin** | The owner role. It can do everything, including things added in future updates. |

## Catalog

| Term | Meaning |
|---|---|
| **Category** | The main grouping of products, e.g. *Air Conditioner*, *Refrigerator*. Categories can have a parent (e.g. *Inverter AC* under *Air Conditioner*). **Every product needs a category.** |
| **Sub category** | An extra list of sub-groups per category. Currently it is a reference list only – products are not linked to it; use parent categories for a product hierarchy. |
| **Brand** | The manufacturer, e.g. *Samsung*, *Walton*. Optional on a product. |
| **Tag** | Free labels for searching and grouping, e.g. *Eid offer*, *Energy saving*. A product can have many. |
| **Simple product** | A product with one price and one stock count per warehouse, e.g. *Walton 1.5 Ton Inverter AC WSI-KRYSTALINE-18C*. |
| **Product with variations** | A product sold in several versions that differ by an attribute, e.g. a fan in *Colour: White / Brown* and *Size: 48" / 56"*. Each version (**variation**) has its own SKU, price and stock. |
| **Attribute** | A property that creates variations, e.g. *Colour*, *Size*, *Capacity*. |
| **Attribute value** | One option of an attribute, e.g. *White*, *56"*, *1.5 Ton*. |
| **SKU** | *Stock Keeping Unit* – your own short unique code for a product or variation, e.g. `WAL-AC-15T`. |
| **Barcode** | The code printed on the box (EAN/UPC). Scanning it at the POS adds the product. |
| **Selling price** | The normal price. |
| **Discount price** | A lower offer price. When set, the POS sells at this price. |
| **Alert quantity** | The stock level per warehouse at which a product is considered "low". |
| **Unit / Base unit** | Measurement names (e.g. *Piece* under *Quantity*). Reference only at the moment; the POS sells whole quantities. |
| **Tax** | A tax rate (percentage or fixed). It can be assigned to products, but tax is **not yet calculated** at the POS – invoices show tax 0. |
| **Warranty template** | A ready-made warranty text (e.g. *Compressor: 10 years, Parts: 1 year*) that the cashier can pick at the POS; it is printed on the invoice. |

## Stock

| Term | Meaning |
|---|---|
| **Stock in** | Adds quantity to a warehouse (new purchase, customer return). |
| **Stock out** | Removes quantity (damaged, own use, gift). |
| **Transfer** | Moves quantity from one warehouse to another. |
| **Set quantity (adjust)** | Overwrites the quantity with the number you counted (stock-taking). |
| **Stock movement** | The history record written for each of the above, with who, when, why and a reference. |

## Selling

| Term | Meaning |
|---|---|
| **POS** | *Point of sale* – the selling screen used at the counter. |
| **POS session** | A cashier's shift. It is opened with a branch, a **warehouse** (stock is taken from here) and opening cash, and closed at the end of the day. You cannot sell without an open session. |
| **Walk-in customer** | A sale without a saved customer. |
| **Draft** | A saved, unfinished sale (a "hold"). It does not reduce stock and has no invoice number. |
| **Completed sale** | A finished sale: stock is reduced and an invoice number like `POS-20261025143010-15` is given. |
| **Void** | Cancelling a completed or draft sale. Stock of a completed sale goes back to its warehouse. It cannot be undone. |
| **Paid / Partial / Unpaid** | Payment status of an order, worked out from the payments. |
| **Due** | The amount the customer still owes on an order. Collect it later with **Pay due**. |
| **Split payment** | Paying one order with several methods, e.g. part cash + part bKash. |
| **Change** | Money given back when the customer pays more than the total. |

## Credit sales

| Term | Meaning |
|---|---|
| **Installment (hire purchase)** | The shop's own credit: the customer pays a **down payment** now and the rest in scheduled installments to the shop. |
| **Installment plan** | A saved set of terms (number of installments, frequency, interest, fees) the cashier can pick at the POS. |
| **Agreement** | The installment contract of one sale, numbered like `INS-261025-000123`, with its schedule. |
| **Down payment** | The money paid at the time of sale. |
| **Financed amount** | Sale amount + processing fee − down payment. |
| **Flat interest** | Interest = financed amount × rate, once, spread evenly. |
| **Reducing balance interest** | Bank-style interest on the remaining balance (annual rate). |
| **Processing fee** | A one-time fee added to the financed amount. |
| **Late fee** | Charged once per installment that is unpaid more than the **grace days** after its due date. |
| **Bank EMI** | The customer pays by credit card on the bank's POS terminal and the bank converts it to monthly payments. The shop gets paid by the bank later (**settlement**). |
| **Tenure** | Number of months of a bank EMI, each with a **Customer %** (added to the bill) and **Bank %** (deducted by the bank at settlement). |
