# Troubleshooting and good practice

## Common questions

**I cannot save a product.** Create at least one **warehouse** and **category** first, and fill the selling price. SKU, barcode and product code must be unique.

**The POS says "No open session".** Click **Open Session** and choose a branch and warehouse.

**The POS says "Not enough stock".** The quantity is limited to the stock in the **session's warehouse**. Transfer stock to that warehouse or open the session with the right warehouse.

**A product with variations shows 0 stock.** New variations start at 0 – do a **Stock in** for each variation.

**The customer is not found at the POS.** Add them in **Sales → Customers** first, then search again by phone.

**Stock did not come back after deleting an order.** Only **Void** returns stock. Restore the order from Trash, void it, then trash it if needed.

**Bangla text looks broken or boxes appear.** Choose another **Bangla font** in **General Settings → Bangla & appearance** (Noto Sans Bengali is the most complete). For offline PCs choose *SolaimanLipi / Kalpurush / Nikosh* and install that font on the computer.

**Numbers show in English digits while Bangla is selected.** Switch on **Show Bangla digits** in **Bangla & appearance**.

**SMS are not sent.** Check: the **SMS sending** master switch is on, the event switch is on, a default gateway exists and has balance (**Check balance**), and the customer's phone is valid. See **SMS History** for the error.

**Late fees / reminders never run.** Your server must run the Laravel scheduler every minute (`php artisan schedule:run`). Ask your IT person.

**The map does not show.** Paste the full *Embed a map* code from Google Maps, not a `maps.app.goo.gl` short link.

**The contact form says "Too many messages".** The anti-spam limit was reached. Wait an hour, or call the shop.

## Daily checklist

1. Open the POS session with the correct warehouse and opening cash.
2. Sell; collect dues and installments as customers come.
3. At closing: **Sales Report → Today**, compare **Collected** with cash + bKash/bank.
4. Close the POS session with the closing cash.
5. Read new website **Contact messages**.

## Monthly checklist

- Stock count and **Set quantity** corrections.
- Check **Installments → overdue** and call customers.
- **Bank EMI → Pending settlements** – match with bank statements and **Mark settled**.
- Export the monthly sales report (PDF/Excel) for your accountant.

## For the IT administrator

- Cron: `* * * * * php /path/to/artisan schedule:run` (late fees and reminders run daily at 09:00).
- If SMS delivery mode is *Queue worker*: keep `php artisan queue:work` running (Supervisor).
- Run `php artisan storage:link` once so logos, product photos and blog images are visible.
- After updates: `php artisan migrate --force`, `php artisan optimize`, then build assets (`npm run build`).
- Back up the database and the `storage/app/public` folder every day.
- Use HTTPS and set `APP_ENV=production`, `APP_DEBUG=false`.
