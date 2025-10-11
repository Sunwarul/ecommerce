# Sprint 03 — Inventory Core
## Goal
Trusted on-hand stock via immutable ledger; adjustments and low-stock alerts.

## Stories
- StockLedger service
- stocks (materialized) and stock_ledger_entries
- Adjustments (OPENING/DAMAGE/AUDIT) with approval
- Low stock thresholds & list

## Data Model
- stocks
- stock_ledger_entries
- stock_adjustments
- inventory_settings (thresholds)

## Pages
- Stock overview
- Create/approve adjustment
- Low stock list

## DoD
- On-hand equals ledger sum
- Adjustments generate ledger entries
_Last updated 2025-10-11_
