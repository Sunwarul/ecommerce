# Task Tracker - Multi-Branch Showroom Management System

## Sprint 0 - Project Bootstrap & Tooling ✅ COMPLETED

### Foundation Setup

-   [x] Laravel 12+ project setup with PHP 8.3+
-   [x] Vue 3 + Inertia 2 + PrimeVue + Tailwind CSS integration
-   [x] Docker setup (nginx, php, mysql, node)
-   [x] Basic authentication with Laravel Breeze
-   [x] Email verification setup
-   [x] Basic routing structure (web.php, api.php, auth.php)
-   [x] Pest testing framework setup
-   [x] Basic CI/CD with GitHub Actions
-   [x] Code quality tools (Laravel Pint, ESLint, Prettier)

### Core Infrastructure

-   [x] Generic CRUD traits (HasCrud, HasApiCrud)
-   [x] CrudConfig and CrudRouter utilities
-   [x] BaseModel with common functionality
-   [x] Generic CRUD Vue components
-   [x] Base service interface and trait
-   [x] API response wrapper
-   [x] Basic form request validation structure

---

## Sprint 1 - Branches, RBAC, User & Role Management

### Database & Models

-   [ ] Create branches migration (id, name, code, address, phone, is_active, timestamps)
-   [ ] Create branch_user pivot migration (user_id, branch_id, role_id)
-   [ ] Create Branch model with relationships
-   [ ] Update User model to add phone, is_super_admin fields
-   [ ] Add branch relationships to User model
-   [ ] Create BelongsToBranch trait for global scoping
-   [ ] Install and configure spatie/laravel-permission
-   [ ] Create permission seeder (Super Admin, Branch Manager, Sales, Inventory, Accounts, Service)
-   [ ] Create role seeder with default permissions

### Middleware & Scoping

-   [ ] Create BranchContext middleware
-   [ ] Register BranchContext middleware in kernel
-   [ ] Implement branch resolution logic in middleware
-   [ ] Add global scope to BelongsToBranch trait
-   [ ] Create branch switcher component for frontend
-   [ ] Add branch context to all authenticated routes

### Controllers & Policies

-   [ ] Create BranchController with CRUD operations
-   [ ] Create BranchPolicy (Super Admin only for CRUD)
-   [ ] Create BranchStoreRequest and BranchUpdateRequest
-   [ ] Update UserController to handle branch assignments
-   [ ] Create UserBranchController for branch-user management
-   [ ] Add branch scoping to existing controllers

### Frontend Components

-   [ ] Create Branch index page with DataTable
-   [ ] Create Branch create/edit dialog
-   [ ] Create Branch switcher in top navigation
-   [ ] Update User management to show branch assignments
-   [ ] Add branch filter to existing CRUD pages
-   [ ] Create branch context store (Pinia)

### Testing

-   [ ] Create Branch model tests
-   [ ] Create BranchController tests
-   [ ] Create BranchPolicy tests
-   [ ] Create BranchContext middleware tests
-   [ ] Test branch scoping in existing controllers

---

## Sprint 2 - Master Data: Brands, Categories, Taxes, Units, Attributes & Variants, Products

### Database & Models

-   [ ] Create brands migration (id, name, description, logo, is_active, branch_id)
-   [ ] Create categories migration (id, name, description, parent_id, is_active, branch_id)
-   [ ] Create units migration (id, name, symbol, is_active, branch_id)
-   [ ] Create taxes migration (id, name, rate, type, is_active, branch_id)
-   [ ] Create attributes migration (id, name, type, is_active, branch_id)
-   [ ] Create attribute_values migration (id, attribute_id, value, is_active)
-   [ ] Update products migration to add branch_id, warranty_months, guarantee_months
-   [ ] Update product_variants migration to add branch_id, cost_price, mrp
-   [ ] Create Brand model with BelongsToBranch trait
-   [ ] Create Category model with BelongsToBranch trait and tree relationships
-   [ ] Create Unit model with BelongsToBranch trait
-   [ ] Create Tax model with BelongsToBranch trait
-   [ ] Create Attribute model with BelongsToBranch trait
-   [ ] Create AttributeValue model
-   [ ] Update Product model with new relationships and branch scoping
-   [ ] Update ProductVariation model with branch scoping

### Services & Business Logic

-   [ ] Create ProductVariantGenerator service
-   [ ] Create SKUGenerator service
-   [ ] Create BarcodeGenerator service
-   [ ] Create AttributeMatrixGenerator service
-   [ ] Implement variant creation from attribute combinations
-   [ ] Add uniqueness validation for SKU/barcode per branch

### Controllers & Policies

-   [ ] Create BrandController with branch scoping
-   [ ] Create CategoryController with branch scoping and tree operations
-   [ ] Create UnitController with branch scoping
-   [ ] Create TaxController with branch scoping
-   [ ] Create AttributeController with branch scoping
-   [ ] Create AttributeValueController
-   [ ] Update ProductController with variant generation
-   [ ] Create ProductVariantController
-   [ ] Create BrandPolicy, CategoryPolicy, etc.
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Brand index/create/edit pages
-   [ ] Create Category index/create/edit pages with tree view
-   [ ] Create Unit index/create/edit pages
-   [ ] Create Tax index/create/edit pages
-   [ ] Create Attribute index/create/edit pages
-   [ ] Create AttributeValue management interface
-   [ ] Update Product create/edit with variant matrix generator
-   [ ] Create ProductVariant management interface
-   [ ] Add image upload component (spatie/medialibrary)
-   [ ] Create attribute selection component for products

### Media Management

-   [ ] Install and configure spatie/laravel-medialibrary
-   [ ] Add media relationships to Product and Brand models
-   [ ] Create media upload service
-   [ ] Create image gallery component
-   [ ] Implement image resizing and optimization

### Testing

-   [ ] Create Brand model and controller tests
-   [ ] Create Category model and controller tests
-   [ ] Create Unit, Tax, Attribute model tests
-   [ ] Create ProductVariantGenerator service tests
-   [ ] Create SKU/Barcode generator tests
-   [ ] Test branch scoping for all catalog entities

---

## Sprint 3 - Inventory Core: Stock Ledger, Adjustments, Opening Balances, Alerts

### Database & Models

-   [ ] Create stock_ledger_entries migration (branch_id, product_variant_id, qty_change, unit_cost, ref_type, ref_id, remarks, occurred_at)
-   [ ] Create stocks migration (branch_id, product_variant_id, qty) - materialized view
-   [ ] Create stock_adjustments migration (id, branch_id, type, status, remarks, created_by, approved_by, approved_at)
-   [ ] Create stock_adjustment_lines migration (adjustment_id, product_variant_id, qty_change, unit_cost, remarks)
-   [ ] Create inventory_settings migration (branch_id, low_stock_threshold, overstock_threshold)
-   [ ] Create StockLedgerEntry model
-   [ ] Create Stock model (materialized)
-   [ ] Create StockAdjustment model
-   [ ] Create StockAdjustmentLine model
-   [ ] Create InventorySetting model

### Services & Business Logic

-   [ ] Create StockLedger service (single source of truth)
-   [ ] Implement stock movement methods (increase, decrease, transfer)
-   [ ] Create stock recalculation service
-   [ ] Create low stock detection service
-   [ ] Create stock adjustment workflow service
-   [ ] Implement stock validation rules
-   [ ] Create stock history tracking

### Controllers & Policies

-   [ ] Create StockController for stock overview
-   [ ] Create StockAdjustmentController
-   [ ] Create StockLedgerController for history
-   [ ] Create InventorySettingController
-   [ ] Create StockPolicy
-   [ ] Create StockAdjustmentPolicy
-   [ ] Create form requests for adjustments

### Frontend Components

-   [ ] Create Stock overview page with filters
-   [ ] Create Low stock alerts page
-   [ ] Create Stock adjustment create/edit workflow
-   [ ] Create Stock adjustment approval interface
-   [ ] Create Stock history/ledger view
-   [ ] Create Inventory settings page
-   [ ] Add stock status indicators to product lists

### Background Jobs & Events

-   [ ] Create stock recalculation job
-   [ ] Create low stock alert job
-   [ ] Create stock adjustment approval workflow
-   [ ] Implement stock movement events
-   [ ] Create stock audit trail logging

### Testing

-   [ ] Create StockLedger service tests
-   [ ] Create StockAdjustment workflow tests
-   [ ] Create stock calculation tests
-   [ ] Test stock movement validation
-   [ ] Create inventory setting tests

---

## Sprint 4 - Suppliers & Purchasing (PO → GRN → Purchase Invoice → Payables)

### Database & Models

-   [ ] Create suppliers migration (id, name, code, contact_person, phone, email, address, terms, credit_limit, branch_id)
-   [ ] Create purchase_orders migration (id, branch_id, supplier_id, po_number, status, order_date, expected_date, total_amount, created_by)
-   [ ] Create purchase_order_lines migration (po_id, product_variant_id, qty, unit_cost, total_cost, received_qty)
-   [ ] Create grns migration (id, branch_id, po_id, grn_number, status, received_date, total_amount, created_by)
-   [ ] Create grn_lines migration (grn_id, product_variant_id, qty_received, unit_cost, total_cost, condition)
-   [ ] Create purchase_invoices migration (id, branch_id, supplier_id, invoice_number, invoice_date, due_date, total_amount, paid_amount, status)
-   [ ] Create purchase_invoice_lines migration (invoice_id, product_variant_id, qty, unit_cost, total_cost)
-   [ ] Create purchase_returns migration (id, branch_id, supplier_id, return_number, return_date, reason, total_amount)
-   [ ] Create purchase_return_lines migration (return_id, product_variant_id, qty, unit_cost, total_cost)
-   [ ] Create expenses migration (id, branch_id, type, amount, description, allocated_to, created_by)
-   [ ] Create Supplier model with BelongsToBranch trait
-   [ ] Create PurchaseOrder model
-   [ ] Create PurchaseOrderLine model
-   [ ] Create GRN model
-   [ ] Create GRNLine model
-   [ ] Create PurchaseInvoice model
-   [ ] Create PurchaseInvoiceLine model
-   [ ] Create PurchaseReturn model
-   [ ] Create PurchaseReturnLine model
-   [ ] Create Expense model

### Services & Business Logic

-   [ ] Create PurchaseOrderService
-   [ ] Create GRNService
-   [ ] Create PurchaseInvoiceService
-   [ ] Create PurchaseReturnService
-   [ ] Create ExpenseService
-   [ ] Implement PO → GRN → Invoice workflow
-   [ ] Create landed cost allocation service
-   [ ] Create payable tracking service
-   [ ] Implement 2-way/3-way matching

### Controllers & Policies

-   [ ] Create SupplierController
-   [ ] Create PurchaseOrderController
-   [ ] Create GRNController
-   [ ] Create PurchaseInvoiceController
-   [ ] Create PurchaseReturnController
-   [ ] Create ExpenseController
-   [ ] Create SupplierPolicy
-   [ ] Create PurchaseOrderPolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Supplier index/create/edit pages
-   [ ] Create Purchase Order create/edit workflow
-   [ ] Create GRN create/edit interface
-   [ ] Create Invoice matching interface
-   [ ] Create Purchase Return interface
-   [ ] Create Expense management interface
-   [ ] Create payable tracking dashboard
-   [ ] Add supplier selection components

### Workflow Management

-   [ ] Implement PO approval workflow
-   [ ] Create GRN receiving workflow
-   [ ] Implement invoice matching workflow
-   [ ] Create return processing workflow
-   [ ] Add workflow status tracking

### Testing

-   [ ] Create Supplier model and controller tests
-   [ ] Create PurchaseOrder workflow tests
-   [ ] Create GRN processing tests
-   [ ] Create invoice matching tests
-   [ ] Test payable calculations

---

## Sprint 5 - Customers (CRM), Pricing & POS (Cash/Credit/Partial), Invoicing

### Database & Models

-   [ ] Update customers migration (add credit_limit, default_terms, branch_id)
-   [ ] Create sales migration (id, branch_id, customer_id, sale_number, sale_date, type, status, subtotal, tax_amount, discount_amount, total_amount, paid_amount, due_amount)
-   [ ] Create sale_lines migration (sale_id, product_variant_id, qty, unit_price, discount_amount, tax_amount, total_amount)
-   [ ] Create payments migration (id, payable_type, payable_id, amount, payment_method, payment_date, reference, branch_id)
-   [ ] Create customer_ledgers migration (id, customer_id, transaction_type, transaction_id, debit_amount, credit_amount, balance, transaction_date)
-   [ ] Update Customer model with branch scoping
-   [ ] Create Sale model
-   [ ] Create SaleLine model
-   [ ] Create Payment model (polymorphic)
-   [ ] Create CustomerLedger model

### Services & Business Logic

-   [ ] Create PosService (pricing/tax/discount/payments)
-   [ ] Create CustomerService
-   [ ] Create SaleService
-   [ ] Create PaymentService
-   [ ] Create CustomerLedgerService
-   [ ] Implement dynamic pricing engine
-   [ ] Create tax calculation service
-   [ ] Create discount calculation service
-   [ ] Implement partial payment handling
-   [ ] Create invoice generation service

### Controllers & Policies

-   [ ] Create CustomerController with branch scoping
-   [ ] Create SaleController
-   [ ] Create PaymentController
-   [ ] Create CustomerLedgerController
-   [ ] Create PosController
-   [ ] Create CustomerPolicy
-   [ ] Create SalePolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Customer index/create/edit pages
-   [ ] Create POS interface (barcode entry, cart, payment)
-   [ ] Create Sale create/edit workflow
-   [ ] Create Payment processing interface
-   [ ] Create Customer ledger view
-   [ ] Create Invoice viewer/PDF generator
-   [ ] Add customer search/selection components
-   [ ] Create payment method selection

### POS Features

-   [ ] Implement barcode scanning
-   [ ] Create cart management
-   [ ] Add quick product search
-   [ ] Implement hold/resume sale
-   [ ] Create payment modal
-   [ ] Add receipt printing

### Testing

-   [ ] Create Customer model and controller tests
-   [ ] Create PosService tests
-   [ ] Create Sale workflow tests
-   [ ] Create Payment processing tests
-   [ ] Test pricing calculations

---

## Sprint 6 - EMI (Installments): Plans, Schedules, Penalties, Receipts, Reminders

### Database & Models

-   [ ] Create emi_plans migration (id, name, tenor_months, interest_type, interest_rate, late_fee_type, late_fee_value, is_active, branch_id)
-   [ ] Create emi_contracts migration (id, sale_id, plan_id, principal, down_payment, financed_amount, start_date, status, branch_id)
-   [ ] Create emi_schedules migration (id, contract_id, installment_no, due_date, principal_due, interest_due, total_due, paid_at, penalty, branch_id)
-   [ ] Create emi_receipts migration (id, schedule_id, amount, payment_method, payment_date, reference, branch_id)
-   [ ] Create EmiPlan model
-   [ ] Create EmiContract model
-   [ ] Create EmiSchedule model
-   [ ] Create EmiReceipt model

### Services & Business Logic

-   [ ] Create EmiScheduler service
-   [ ] Create EmiPlanService
-   [ ] Create EmiContractService
-   [ ] Create EmiReceiptService
-   [ ] Implement amortization calculation (flat/declining)
-   [ ] Create penalty calculation service
-   [ ] Create overdue detection service
-   [ ] Implement receipt allocation logic

### Controllers & Policies

-   [ ] Create EmiPlanController
-   [ ] Create EmiContractController
-   [ ] Create EmiScheduleController
-   [ ] Create EmiReceiptController
-   [ ] Create EmiPlanPolicy
-   [ ] Create EmiContractPolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create EmiPlan index/create/edit pages
-   [ ] Create EmiContract management interface
-   [ ] Create EmiSchedule view with payment interface
-   [ ] Create EmiReceipt generation
-   [ ] Add EMI option to POS
-   [ ] Create EMI plan selection component
-   [ ] Create schedule preview component

### EMI Workflow

-   [ ] Implement EMI sale creation
-   [ ] Create schedule generation
-   [ ] Implement payment processing
-   [ ] Create receipt generation
-   [ ] Add overdue tracking

### Testing

-   [ ] Create EmiScheduler service tests
-   [ ] Create amortization calculation tests
-   [ ] Create EmiContract workflow tests
-   [ ] Create penalty calculation tests
-   [ ] Test receipt allocation logic

---

## Sprint 7 - Returns, Exchanges & Refunds

### Database & Models

-   [ ] Create sales_returns migration (id, branch_id, sale_id, return_number, return_date, reason, status, total_amount, refund_amount)
-   [ ] Create sales_return_lines migration (return_id, product_variant_id, qty, unit_price, total_amount, condition)
-   [ ] Create exchanges migration (id, branch_id, original_sale_id, new_sale_id, exchange_date, price_difference, status)
-   [ ] Create store_credits migration (id, branch_id, customer_id, amount, balance, expiry_date, created_by)
-   [ ] Create SalesReturn model
-   [ ] Create SalesReturnLine model
-   [ ] Create Exchange model
-   [ ] Create StoreCredit model

### Services & Business Logic

-   [ ] Create SalesReturnService
-   [ ] Create ExchangeService
-   [ ] Create StoreCreditService
-   [ ] Implement return validation rules
-   [ ] Create refund processing service
-   [ ] Implement exchange price difference calculation
-   [ ] Create store credit management

### Controllers & Policies

-   [ ] Create SalesReturnController
-   [ ] Create ExchangeController
-   [ ] Create StoreCreditController
-   [ ] Create SalesReturnPolicy
-   [ ] Create ExchangePolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Sales Return interface
-   [ ] Create Exchange processing interface
-   [ ] Create Store Credit management
-   [ ] Add return option to sale history
-   [ ] Create refund processing interface
-   [ ] Create exchange workflow

### Return Workflow

-   [ ] Implement return validation
-   [ ] Create restocking process
-   [ ] Implement refund options
-   [ ] Create exchange workflow
-   [ ] Add return reason tracking

### Testing

-   [ ] Create SalesReturn workflow tests
-   [ ] Create Exchange processing tests
-   [ ] Create refund calculation tests
-   [ ] Test return validation rules

---

## Sprint 8 - Warranty/Guarantee & Service (RMA/Tickets)

### Database & Models

-   [ ] Create warranty_registrations migration (id, sale_id, product_variant_id, serial_no, warranty_start, warranty_end, guarantee_start, guarantee_end, branch_id)
-   [ ] Create service_tickets migration (id, branch_id, customer_id, warranty_registration_id, ticket_number, issue_description, status, priority, assigned_to, created_date, resolved_date)
-   [ ] Create service_actions migration (id, ticket_id, action_type, description, parts_used, cost, performed_by, performed_date)
-   [ ] Create rma migration (id, branch_id, ticket_id, vendor_id, rma_number, status, created_date, resolved_date)
-   [ ] Create WarrantyRegistration model
-   [ ] Create ServiceTicket model
-   [ ] Create ServiceAction model
-   [ ] Create RMA model

### Services & Business Logic

-   [ ] Create WarrantyService
-   [ ] Create ServiceTicketService
-   [ ] Create ServiceActionService
-   [ ] Create RMAService
-   [ ] Implement warranty validation
-   [ ] Create ticket workflow management
-   [ ] Implement parts consumption tracking
-   [ ] Create RMA processing workflow

### Controllers & Policies

-   [ ] Create WarrantyRegistrationController
-   [ ] Create ServiceTicketController
-   [ ] Create ServiceActionController
-   [ ] Create RMAController
-   [ ] Create WarrantyPolicy
-   [ ] Create ServiceTicketPolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Warranty registration interface
-   [ ] Create Service ticket management
-   [ ] Create Service action tracking
-   [ ] Create RMA processing interface
-   [ ] Add warranty lookup functionality
-   [ ] Create technician dashboard
-   [ ] Create job sheet printing

### Service Workflow

-   [ ] Implement ticket lifecycle
-   [ ] Create parts tracking
-   [ ] Implement RMA workflow
-   [ ] Add warranty expiry notifications
-   [ ] Create service reporting

### Testing

-   [ ] Create WarrantyService tests
-   [ ] Create ServiceTicket workflow tests
-   [ ] Create RMA processing tests
-   [ ] Test warranty validation

---

## Sprint 9 - Notifications & Templates (Due/EMI/Warranty/Low Stock)

### Database & Models

-   [ ] Create notification_templates migration (id, code, name, subject, body, channels, is_active, branch_id)
-   [ ] Create notification_jobs migration (id, template_id, recipient_type, recipient_id, payload, scheduled_at, status, sent_at, error_message)
-   [ ] Create user_contact_channels migration (id, user_id, channel_type, channel_value, is_verified, is_active)
-   [ ] Create NotificationTemplate model
-   [ ] Create NotificationJob model
-   [ ] Create UserContactChannel model

### Services & Business Logic

-   [ ] Create NotificationService
-   [ ] Create NotificationTemplateService
-   [ ] Create NotificationJobService
-   [ ] Implement template rendering with placeholders
-   [ ] Create channel abstractions (SMS, Email, WhatsApp)
-   [ ] Create notification scheduling service
-   [ ] Implement delivery tracking

### Controllers & Policies

-   [ ] Create NotificationTemplateController
-   [ ] Create NotificationJobController
-   [ ] Create UserContactChannelController
-   [ ] Create NotificationTemplatePolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Notification template management
-   [ ] Create Notification job monitoring
-   [ ] Create Contact channel management
-   [ ] Create template preview interface
-   [ ] Add notification center
-   [ ] Create delivery status tracking

### Notification Hooks

-   [ ] Implement sales due reminders
-   [ ] Create EMI upcoming/overdue notifications
-   [ ] Add warranty expiry alerts
-   [ ] Create low stock notifications
-   [ ] Implement payment reminders

### Testing

-   [ ] Create NotificationService tests
-   [ ] Create template rendering tests
-   [ ] Create channel delivery tests
-   [ ] Test notification scheduling

---

## Sprint 10 - Inter-Branch Transfers & Reservations

### Database & Models

-   [ ] Create stock_transfers migration (id, from_branch_id, to_branch_id, transfer_number, status, requested_date, shipped_date, received_date, total_amount)
-   [ ] Create stock_transfer_lines migration (transfer_id, product_variant_id, qty_requested, qty_shipped, qty_received, unit_cost, total_cost)
-   [ ] Create reservations migration (id, branch_id, product_variant_id, qty, ref_type, ref_id, expires_at, created_by)
-   [ ] Create StockTransfer model
-   [ ] Create StockTransferLine model
-   [ ] Create Reservation model

### Services & Business Logic

-   [ ] Create StockTransferService
-   [ ] Create ReservationService
-   [ ] Implement transfer workflow (Request → Approve → Ship → Receive)
-   [ ] Create reservation management
-   [ ] Implement in-transit tracking
-   [ ] Create transfer validation

### Controllers & Policies

-   [ ] Create StockTransferController
-   [ ] Create ReservationController
-   [ ] Create StockTransferPolicy
-   [ ] Create ReservationPolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Stock transfer request interface
-   [ ] Create Transfer approval workflow
-   [ ] Create Ship/receive interface
-   [ ] Create Reservation management
-   [ ] Add transfer tracking dashboard
-   [ ] Create reservation monitoring

### Transfer Workflow

-   [ ] Implement request creation
-   [ ] Create approval process
-   [ ] Implement shipping workflow
-   [ ] Create receiving process
-   [ ] Add transfer balancing

### Testing

-   [ ] Create StockTransfer workflow tests
-   [ ] Create Reservation management tests
-   [ ] Test transfer validation
-   [ ] Create in-transit tracking tests

---

## Sprint 11 - Accounting Lite: Ledgers, Cash/Bank, Receivables/Payables, P&L Snapshot

### Database & Models

-   [ ] Create accounts migration (id, code, name, type, parent_id, is_active, branch_id)
-   [ ] Create journals migration (id, branch_id, journal_number, date, description, total_debit, total_credit, created_by)
-   [ ] Create journal_lines migration (id, journal_id, account_id, debit_amount, credit_amount, description, reference_type, reference_id)
-   [ ] Create Account model
-   [ ] Create Journal model
-   [ ] Create JournalLine model

### Services & Business Logic

-   [ ] Create JournalPoster service
-   [ ] Create AccountService
-   [ ] Create JournalService
-   [ ] Implement posting rules for all transactions
-   [ ] Create account balance calculation
-   [ ] Implement trial balance generation
-   [ ] Create P&L calculation service

### Controllers & Policies

-   [ ] Create AccountController
-   [ ] Create JournalController
-   [ ] Create AccountPolicy
-   [ ] Create JournalPolicy
-   [ ] Create form requests for all entities

### Frontend Components

-   [ ] Create Account management interface
-   [ ] Create Journal entry interface
-   [ ] Create Trial balance view
-   [ ] Create P&L statement
-   [ ] Create Cash/Bank book
-   [ ] Create Receivables/Payables reports

### Accounting Integration

-   [ ] Implement automatic journal posting
-   [ ] Create account reconciliation
-   [ ] Implement period closing
-   [ ] Create financial reporting

### Testing

-   [ ] Create JournalPoster service tests
-   [ ] Create posting rules tests
-   [ ] Create account balance tests
-   [ ] Test P&L calculations

---

## Sprint 12 - Dashboards & Reporting

### Services & Business Logic

-   [ ] Create DashboardService
-   [ ] Create ReportService
-   [ ] Implement KPI calculations
-   [ ] Create report data aggregation
-   [ ] Implement caching for heavy reports
-   [ ] Create report scheduling

### Controllers & Policies

-   [ ] Create DashboardController
-   [ ] Create ReportController
-   [ ] Create DashboardPolicy
-   [ ] Create ReportPolicy

### Frontend Components

-   [ ] Create Super Admin dashboard
-   [ ] Create Branch dashboard
-   [ ] Create Sales reports
-   [ ] Create Purchase reports
-   [ ] Create Stock reports
-   [ ] Create Customer reports
-   [ ] Create Supplier reports
-   [ ] Create EMI reports
-   [ ] Create Service reports
-   [ ] Add report filters and exports

### Report Types

-   [ ] Sales by date/branch/user/product/brand
-   [ ] Purchase reports
-   [ ] Stock movement/aging
-   [ ] Low stock reports
-   [ ] Profit by invoice/product
-   [ ] Customer ledger
-   [ ] Supplier ledger
-   [ ] EMI status reports
-   [ ] Warranty expiry reports
-   [ ] Service turnaround reports

### Testing

-   [ ] Create DashboardService tests
-   [ ] Create ReportService tests
-   [ ] Test KPI calculations
-   [ ] Test report data accuracy

---

## Sprint 13 - Data Import/Export, Audit, Backups

### Database & Models

-   [ ] Create imports migration (id, type, status, file_path, errors_json, created_by, branch_id)
-   [ ] Create backups migration (id, type, file_path, size, status, created_by, branch_id)
-   [ ] Create Import model
-   [ ] Create Backup model

### Services & Business Logic

-   [ ] Create ImportService
-   [ ] Create BackupService
-   [ ] Implement CSV/XLSX importers
-   [ ] Create chunked import processing
-   [ ] Implement backup scheduling
-   [ ] Create restore functionality

### Controllers & Policies

-   [ ] Create ImportController
-   [ ] Create BackupController
-   [ ] Create ImportPolicy
-   [ ] Create BackupPolicy

### Frontend Components

-   [ ] Create Import wizard interface
-   [ ] Create Import progress tracking
-   [ ] Create Backup management interface
-   [ ] Create Audit log browser
-   [ ] Add import templates
-   [ ] Create error reporting interface

### Import Types

-   [ ] Product import
-   [ ] Customer import
-   [ ] Supplier import
-   [ ] Opening stock import
-   [ ] Price import

### Testing

-   [ ] Create ImportService tests
-   [ ] Create BackupService tests
-   [ ] Test import validation
-   [ ] Test backup/restore process

---

## Sprint 14 - Performance, Security Hardening, QA/UAT Stabilization

### Performance Optimization

-   [ ] Add database indexes on frequently queried columns
-   [ ] Implement query optimization
-   [ ] Add Redis caching for heavy operations
-   [ ] Implement HTTP caching/ETags
-   [ ] Add lazy loading for relationships
-   [ ] Optimize N+1 queries

### Security Hardening

-   [ ] Implement rate limiting on API endpoints
-   [ ] Add content security policy
-   [ ] Implement input sanitization
-   [ ] Add SQL injection protection
-   [ ] Implement XSS protection
-   [ ] Add CSRF protection

### Quality Assurance

-   [ ] Create comprehensive test suite
-   [ ] Implement browser testing (Laravel Dusk)
-   [ ] Add accessibility testing
-   [ ] Create performance testing
-   [ ] Implement security testing
-   [ ] Add regression testing

### Documentation

-   [ ] Create API documentation
-   [ ] Create user manuals
-   [ ] Create admin guides
-   [ ] Create developer documentation
-   [ ] Create deployment guides

### Testing

-   [ ] Create performance tests
-   [ ] Create security tests
-   [ ] Create accessibility tests
-   [ ] Test all critical user flows

---

## Sprint 15 - Deployment, Documentation, Handover

### Deployment Setup

-   [ ] Create production Docker configuration
-   [ ] Set up environment-specific configs
-   [ ] Implement migration pipelines
-   [ ] Create deployment scripts
-   [ ] Set up monitoring and alerting
-   [ ] Configure backup strategies

### Documentation

-   [ ] Create runbooks
-   [ ] Create admin manuals
-   [ ] Create data dictionary
-   [ ] Create API documentation
-   [ ] Create troubleshooting guides

### Monitoring & Alerting

-   [ ] Set up health checks
-   [ ] Configure queue monitoring
-   [ ] Set up error rate monitoring
-   [ ] Create performance monitoring
-   [ ] Implement alerting rules

### Handover

-   [ ] Create user training materials
-   [ ] Conduct user training sessions
-   [ ] Create admin training
-   [ ] Document maintenance procedures
-   [ ] Create support procedures

### Testing

-   [ ] Create deployment tests
-   [ ] Test monitoring systems
-   [ ] Validate backup/restore
-   [ ] Test alerting systems

---

## Summary

**Total Tasks**: ~400+ individual tasks
**Estimated Timeline**: 15 sprints (30-45 weeks with 1-2 developers)
**Current Status**: Sprint 0 completed, Sprint 1-2 in progress

Each task is designed to be completed in 1-2 hours and can be treated as a single PR. Tasks are organized by sprint and domain, making it easy to delegate to different developers based on their expertise areas.
