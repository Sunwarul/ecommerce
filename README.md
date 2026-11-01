# Retail POS & Showroom Management

Point of sale, inventory and showroom management built with Laravel 12, Inertia and Vue 3 (PrimeVue 4).

**Features**
- POS with barcode search, drafts, partial payments, A4 and thermal invoices
- Installment (hire purchase) sales: plans, schedules, late fees, collection sheet, guarantor/NID
- Bank card EMI tracking with bank rates and settlement follow-up
- SMS: event messages (sale, payment, installment reminders), bulk campaigns, history, gateway setup and balance
- Sales reports with presets, charts and PDF / CSV / Excel export
- Products with variations, multi-warehouse stock and stock movements
- Branches, roles & permissions, customers, suppliers, expenses
- English, বাংলা, العربية (RTL), Español, हिन्दी; light and dark themes; works on phones

**Local development**

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate   # set APP_ENV=local, APP_DEBUG=true for development
php artisan migrate --seed                         # includes demo data
composer dev                                       # server, queue, logs and Vite
```

See [DEPLOYMENT.md](DEPLOYMENT.md) for production setup.
