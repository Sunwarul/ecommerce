# Architecture

## Bounded Contexts (folders under `app/Domain` suggested)
- Auth & RBAC
- Branching (BranchContext middleware, BelongsToBranch trait)
- Catalog (Brands, Categories, Attributes/Variants, Products, Media)
- Inventory (Stock ledger, Adjustments, Transfers, Reservations)
- Purchasing (Suppliers, PO, GRN, Invoices, Returns, Landed cost)
- Sales/POS (Customers, Sales, Payments, Returns, Exchanges)
- EMI (Plans, Contracts, Schedules, Receipts, Penalties)
- Service/Warranty (Warranty registration, Tickets, Parts usage)
- Notifications (Templates, Jobs, Channels)
- Accounting (Accounts, Journals, Posting rules)
- Reporting (Dashboards, reports, rollups)
- System (Imports, Backups, Activity log)

## Multi-Branch Rules
- All tenant data carries `branch_id`. Never trust client `branch_id`; infer from server-side BranchContext.
- Super Admin bypasses branch filters; others are scoped.
- Use global scope via a `BelongsToBranch` trait where safe.

## Data Integrity
- ULIDs for primary keys; soft deletes on tenant tables.
- Foreign keys + indexes on FK and frequent filter columns.
- Stock truth: immutable `stock_ledger_entries` (+ derived `stocks`).

## Services
- StockLedger (single entry point for stock movements)
- PosService (pricing/tax/discount/payments)
- EmiScheduler (amortization, penalties)
- JournalPoster (map domain events to journal entries)
- Notifier (template rendering and channel dispatch)
