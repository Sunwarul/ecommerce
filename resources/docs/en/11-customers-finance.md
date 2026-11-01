# Customers, suppliers, expenses and payment methods

## Customers

**Sales → Customers**. Required: **Name** and **Phone**. Optional: customer type (*Retail* / *Wholesale*), email, WhatsApp number, tax number, address, opening balance, credit limit, photo and **ID document** (NID, passport, driving licence – PDF, Word, JPG or PNG up to 7 MB).

Search works with partial phone numbers and `*` wildcards. Add the customer before an installment sale; the POS only searches existing customers.

> Keep the NID copy of every installment customer here – it is useful if a dispute arises.

## Suppliers

**Catalog → Suppliers**: **Code**, **Name**, **Phone**, address, status. Use the supplier code or challan number as the reference when you do a **Stock in**.

## Expenses

**Finance → Expenses**: **Title**, **Date**, **Amount**, **Expense category**, **Spent by**, **Warehouse**, details and an **Attachment (receipt)**. Create categories first in **Finance → Expense Categories** (e.g. *Rent*, *Electricity*, *Salary*, *Transport*, *Refund*).

## Payment methods

**Finance → Payment Methods**: name, active and photo. Only active methods appear at the POS. Suggested list for Bangladesh: *Cash*, *bKash*, *Nagad*, *Rocket*, *Bank transfer*, *Cheque (bank)*, *Card*, **Card EMI**.

- A method whose name contains **bank** asks for bank details (customer bank, account, cheque/transaction no.).
- **Do not delete or rename "Card EMI"** – bank EMI checkout uses it.

## Currencies

**Finance → Currencies** keeps a reference list with exchange rates. Prices are not converted; the currency symbol shown everywhere is set in **General Settings → Bangla & appearance**.
