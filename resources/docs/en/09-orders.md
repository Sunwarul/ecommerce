# Orders: sales history, dues, drafts and voids

Open **Orders** from the top bar or **Sales → Orders** (page title *Sales History*, permission *Order Index*).

## Finding orders

- Search by invoice no., customer name or phone, or cashier (`*` works as a wildcard).
- Filter by order status (*Draft*, *Completed*, *Void*), payment status (*Unpaid*, *Partial*, *Paid*), **Sale type** (*Regular*, *Installment*, *Bank EMI*), dates, and under **More filters** by category, brand or product.
- **Show insights** shows top-selling products and sales by brand for the filtered orders.
- Export the list to PDF, Excel or CSV.
- Roles named *admin*, *manager* or *Super Admin* can switch on **All branches**; others see their current branch.

## Actions on an order

| Action | When | What it does |
|---|---|---|
| **Preview / Open invoice / Print** | Always | Shows or prints the invoice. |
| **Pay due** | The order has due and no active installment agreement | Takes one or more payments, shows *Already paid*, *Current due* and *Due after payment*. Sends the *Payment received* SMS if enabled. |
| **Convert due to installments** | Completed order with due, no agreement yet | Creates an installment agreement for the remaining due. |
| **Collect installment** | The order has an active agreement | Opens the agreement to collect. |
| **Complete draft** | Draft | Takes payment (or installment setup), reduces stock and gives the invoice number. |
| **Edit** | Draft | Opens the draft in the POS. |
| **Edit sale date** | Completed; manager roles with *Order Edit Date* | Moves the sale to another date (and report period). The change is logged. |
| **Void** | Not yet void | Cancels the sale. A completed sale's stock goes back; an active installment agreement and pending EMI records are cancelled. **Cannot be undone.** |
| **Move to trash** | Always | Hides the order. **Stock is not returned** – void first. |

In **Trash** you can **Restore** or **Delete permanently**. Permanent delete also erases the order's items, payments, installment agreement and EMI records – it cannot be undone.

**Bulk actions:** move to trash, restore, delete permanently, and **Transfer to branch** (only completed orders move; stock does not move).

> **Refunds:** voiding does not reverse the payments recorded on the order. If you return money to a customer, record it outside the system (e.g. as an expense with a note) until refunds are added.

## Collecting a due – example

Rahim bought a TV for 45,000 and paid 30,000. Later he pays 10,000:

1. Orders → search *Rahim* → **Pay due**.
2. Method *Cash*, amount 10,000 → **Due after payment** shows 5,000.
3. Save. The order becomes *Partial* until fully paid, then *Paid*.
