# Database Schema (High-Level)

> Create migrations per sprint. Use ULIDs and soft deletes.

## Core
- branches(id, name, code, address, phone, is_active)
- users(id, name, email, phone, is_super_admin, ...)
- branch_user(user_id, branch_id)
- spatie/permission tables (roles, permissions, model_has_roles, ...)

## Catalog
- brands, categories(parent_id), units, taxes(rate, inclusive?)
- attributes, attribute_values
- products(brand_id, category_id, name, model, has_serial, warranty_months, guarantee_months, default_unit_id, default_tax_id)
- product_variants(product_id, sku, barcode, mrp, selling_price, cost_price, attribute_value_ids JSON)
- media (via spatie/medialibrary)

## Inventory
- stock_ledger_entries(branch_id, product_variant_id, qty_change, unit_cost, ref_type, ref_id, occurred_at)
- stocks(branch_id, product_variant_id, qty)  // derived/materialized
- stock_adjustments(type, lines...)
- stock_transfers(from_branch, to_branch, status, lines)
- reservations(branch_id, product_variant_id, qty, ref_type, expires_at)

## Purchasing
- suppliers
- purchase_orders + lines
- grns + lines
- purchase_invoices + lines
- purchase_returns + lines
- expenses (freight, unloading, allocation map)

## Sales & Payments
- customers(credit_limit, terms)
- sales + sale_lines
- payments (polymorphic: sales, purchases)
- sales_returns + lines
- exchanges
- store_credits

## EMI
- emi_plans
- emi_contracts(sale_id, plan_id, principal, down_payment, financed_amount, start_date, status)
- emi_schedules(installment_no, due_date, principal_due, interest_due, total_due, paid_at, penalty)
- emi_receipts

## Service/Warranty
- warranty_registrations(sale_id, product_variant_id, serial_no, warranty_end, guarantee_end)
- service_tickets(status workflow)
- service_actions(parts_used reduce stock)

## Notifications
- notification_templates(code, subject, body, channels)
- notification_jobs(payload, scheduled_at, status)

## Accounting
- accounts, journals, journal_lines
- Posting rules for sale/purchase/payment/returns/COGS movements

## System
- imports(type, status, errors_json)
- backups(metadata)
- activity_log (spatie)
