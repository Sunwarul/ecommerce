# SMS

## Settings

**SMS → SMS Settings** (permission *SMS Settings*).

**General tab**

- **SMS sending** – master switch, **off by default**. When off, nothing is sent.
- **Country code** – 880 for Bangladesh (01712345678 becomes 8801712345678).
- **Delivery mode** – *Send right after the request (no worker needed)* is the safe default.
- **Installment reminder (days before due)** – default 3.
- **Automatic messages** – switch each on separately: *Sale completed*, *Payment received*, *Installment plan created*, *Installment reminder*, *Overdue notice*.

**Gateways tab** – add your SMS vendor: *BulkSMSBD*, *Alpha SMS*, *Greenweb*, *Twilio*, *Vonage* or **Generic HTTP API** (any vendor with an HTTP link), plus *Log only* for testing. Enter the API key/token and **Sender ID**, then use **Test** and **Check balance**. The first gateway becomes the default.

**Templates tab** – edit the text of each message. Placeholders are replaced automatically: `{customer_name}`, `{shop_name}`, `{shop_phone}`, `{date}`, `{invoice_no}`, `{total}`, `{paid}`, `{due}`, `{amount}`, `{agreement_no}`, `{installments}`, `{installment_no}`, `{installment_amount}`, `{next_due_date}`. Event templates can be turned off but not deleted; you can add your own (e.g. *Eid greeting*).

> **Bangla SMS:** a Bangla message uses Unicode, so one SMS part holds 70 characters (67 per part when longer) instead of 160. Keep Bangla templates short to save cost.

## Sending SMS manually

**SMS → Send SMS** (permission *SMS Send*): pick customers or a whole audience (*All customers*, *Customers who purchased*, *Customers with due*, *Active installment customers*, *Overdue installment customers*), add extra numbers, write or choose a template and **Confirm sending**. The part counter shows the cost before sending. Manual messages replace `{customer_name}`, `{phone}`, `{shop_name}` and `{date}`.

## History and dashboard

- **SMS History** – every message with status *Queued*, *Sent* or *Failed*. **Resend failed** and **Retry stuck queued** fix delivery problems.
- **SMS Dashboard** – totals, success rate, parts used, daily delivery, top failure reasons and gateway balance.

An SMS failure never blocks a sale.
