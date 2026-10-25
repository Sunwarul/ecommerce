# Selling at the POS

Open **POS** from the top bar or **Sales → POS** (permission *POS Index*).

## 1. Open a session

You cannot sell until a session is open. Click **Open Session** and enter:

- **Branch**
- **Warehouse** – stock for this session is taken from here
- **Opening Cash** – cash in the drawer at the start (optional)
- Notes

Each cashier has one open session at a time. If you switch branch, the session is closed automatically.

## 2. Add products

- Type in **Search name, SKU or scan barcode**, or scan with a barcode scanner (the scanner presses Enter and the exact match is added).
- Click a product card to add it. A product with variations opens **Select variation**.
- In the cart change the quantity or remove a line. You cannot add more than the stock in the session's warehouse.
- The price is the product's **discount price** if set, otherwise the **selling price**.

## 3. Choose the customer

Search **by name or phone** (partial numbers work: `1712345` finds `01712-345678`). Leave it empty for a **Walk-in customer**. New customers must be added first in **Sales → Customers**. A customer is required for installment sales.

## 4. Discount

**Order discount**: *None*, *Percent %* or *Fixed amount* on the whole order.

## 5. Take payment

In **Payments** add one row per method. **Fill due** puts the remaining amount into a row.

- **Split payment:** e.g. Cash 20,000 + bKash 15,000 with the bKash **Transaction ref**.
- **Bank methods** (name contains "bank") show bank details: customer bank, account no., receiving account, cheque/transaction no.
- **Card EMI** shows the EMI fields – see *Installments & bank EMI*.
- **Partial payment** is allowed: the rest stays as **Due** and can be collected later from **Orders**.
- Paying more than the total shows the **Change** to give back.

A regular sale needs at least one payment. For pure credit use **Sale type: Installment** or record a small part payment.

## 6. Warranty and notes

Type the **Warranty info** or click **Templates** to choose a warranty template (relevant ones for the cart's categories are listed first). It is printed on the invoice.

## 7. Finish

- **Complete** – reduces stock, saves payments, gives an invoice number and opens the invoice.
- **Complete & print** – the same and prints straight away.
- **Save draft** – keeps the cart as a draft (hold) without payment or stock change. Finish it later from **Orders → Complete draft** or **Edit**.

## Invoices

Choose **A4** (full invoice with amount in words, payment list, installment schedule and signature lines) or **Thermal** (80 mm receipt printer). The shop name, address, phone, logo and **Invoice footer text** come from settings. Print again any time from **Orders**.

## 8. Close the session

At the end of the shift click **Close Session**, count the drawer and enter **Closing Cash** and notes.

## Worked example

A customer buys a refrigerator for 85,000 and a voltage stabiliser for 3,500; you give 1,500 discount; they pay 50,000 cash and 37,000 by bKash.

1. Scan both barcodes. 2. Search the customer by phone. 3. Order discount *Fixed amount* 1,500 (total 87,000). 4. Payments: Cash 50,000; bKash 37,000 with the bKash TrxID. 5. Choose the warranty template *Refrigerator – 10 years compressor*. 6. **Complete & print**.
