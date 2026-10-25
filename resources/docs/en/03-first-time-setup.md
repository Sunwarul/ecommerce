# First-time setup checklist

Do these steps **in this order** the first time. Each step links to a chapter with details.

1. **Settings → General Settings** – business name, address and phone (printed on invoices), default language, invoice footer text. In **Bangla & appearance** pick the Bangla font and number style.
2. **Website → Website settings → Branding** – upload your **logo** and browser icon (favicon). The logo is used in the app, on invoices and on the website.
3. **Organization → Branches** – create every shop/outlet (name, code, address, phone).
4. **Organization → Warehouses** – create at least one warehouse. **You cannot save a product until a warehouse exists.**
5. **Catalog → Product setup → Categories** – create your categories. **Every product needs one.**
6. **Brands**, **Tags** and **Warranty & Guarantees** – optional but recommended.
7. **Attributes** and **Attribute Values** – only if you sell products with variations (colour, size, capacity…).
8. **Finance → Payment Methods** – e.g. *Cash*, *bKash*, *Nagad*, *Bank transfer*, *Card*. Keep the automatically created **Card EMI** method – bank EMI needs it.
9. **Sales → Bank EMI → Banks & rates** – add partner banks and their tenures if you offer card EMI.
10. **Sales → Installment Plans** – add your standard installment terms if you sell on installments.
11. **Users & access → Roles** and **Users** – create roles (e.g. *Cashier*, *Manager*) and staff accounts.
12. **Sales → Customers** – add regular customers. Customers must exist before an installment sale (the POS cannot create customers).
13. **Catalog → Products** – add products with opening stock per warehouse.
14. Products with variations: do a **Stock in** for each variation (new variations start at 0).
15. **SMS → SMS Settings** – add an SMS gateway and switch on the messages you want (optional).
16. Open the **POS**, open a session and make a test sale. Void it afterwards.

> **Background jobs (for your IT person):** late fees and installment reminders run every day at 09:00. The server must run `php artisan schedule:run` every minute (cron). If SMS delivery mode is *Queue worker*, `php artisan queue:work` must also run.
