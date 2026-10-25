# Installments and bank EMI

## Installment plans

**Sales → Installment Plans** (permission *Installment Plan Manage*) – reusable terms the cashier picks at the POS (still editable per sale).

| Field | Meaning |
|---|---|
| **Number of installments** | 1–120 |
| **Frequency** | *Monthly*, *Weekly*, *Every 2 weeks* or *Custom (days)* |
| **Interest type** | *No interest*, *Flat % (whole term)* or *Reducing balance (annual %)* |
| **Rate** | Interest rate |
| **Minimum down payment %** | Smallest down payment allowed, as % of the sale |
| **Processing fee** | Fixed amount or % of the sale |
| **Late fee (per missed installment)** | Fixed amount or % of the installment |
| **Grace days before late fee** | 0–90 days |
| **Active (shown at POS)** | |

Deleting a plan never changes existing agreements.

## How the amounts are calculated

- **Financed** = sale amount + processing fee − down payment.
- **Flat**: interest = financed × rate %, once, spread evenly over the installments.
- **Reducing balance**: bank-style amortisation on the remaining balance (annual rate; monthly plans use rate ÷ 12).
- Installments are rounded to whole taka by default (the last one absorbs the difference). Change it in **SMS → SMS Settings → General → Installment rounding**.

**Example:** a 60,000 fridge, 6 monthly installments, flat 10 %, processing fee 1,000, down payment 13,000 → financed 48,000, interest 4,800, total 52,800 → 6 × 8,800.

## Selling on installments

1. At the POS select the **customer** (required).
2. Set **Sale type: Installment** and choose the **Installment plan** (or enter terms). Check the schedule preview and **First due date**.
3. Optional KYC: **Customer NID**, **Guarantor name / phone / NID / address**, **Agreement notes**.
4. Enter the down payment in **Payments** (0 is allowed unless the plan has a minimum).
5. **Complete (installment)**. The invoice shows the schedule and late-fee terms; the agreement gets a number like `INS-261025-000123`.

You can also convert the due of an ordinary sale later with **Orders → Convert due to installments**.

## Collecting installments

**Sales → Installments** shows *Agreements* (tabs: active, overdue, due today, upcoming 7 days, completed, cancelled, defaulted) and a **Collection sheet**. Top cards show *Outstanding*, overdue, due today and *Collected this month*.

Open an agreement → **Collect payment** (permission *Installment Collect*): use **Next installment** or **Full balance**, or type an amount; several methods are allowed. Money is applied to the **oldest installment first, late fee before principal**. When everything is paid the agreement becomes *Completed*.

## Late fees and reminders

- A late fee is added **once per installment** when it is unpaid more than the grace days after its due date. It runs every day at 09:00 and whenever an agreement is opened or collected.
- **Waive fee** removes the unpaid late fee of one installment.
- SMS reminders: *Installment reminder* N days before the due date (default 3) and *Overdue notice* the day after the grace period – both must be switched on in SMS settings.

## Agreement status

Permission *Installment Cancel*:

- **Mark defaulted** – stops late fees and collection.
- **Cancel agreement** – unpaid installments are cancelled (the order's due is not reduced).
- **Reactivate** – brings a cancelled/defaulted agreement back.

## Bank EMI

With bank EMI the card is charged in full on the bank's terminal and the bank pays the shop later. The software tracks what each bank owes you.

1. **Sales → Bank EMI → Banks & rates** (permission *EMI Manage*): add the bank (e.g. *City Bank*, short name *CBL*) and its **Tenures**: months, **Customer %** (added to the customer's bill), **Bank %** (deducted at settlement) and **Min amount**.
2. At the POS add a payment with method **Card EMI**, pick **Bank** and **Tenure**, and enter **Card last 4**, **Approval code** and card holder name. The customer charge appears as **Bank EMI charge**.
3. When the bank pays you, open **Bank EMI → Transactions**, select the rows and **Mark settled** (settlement date, bank reference, amount received). **Revert** undoes a settlement.

**Example:** 100,000 on a 12-month tenure with customer 3 % and bank 2 %: card charged 103,000; customer pays 8,583/month to the bank; the bank pays the shop 100,940 (103,000 − 2 %).
