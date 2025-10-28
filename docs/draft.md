## Sprint 1 - Branches, RBAC, User & Role Management

### **Day 1 Tasks (2-3 tasks)**

**Task 1.1: Database Foundation Setup**
- Create branches migration (id, name, code, address, phone, is_active, timestamps)
- Create branch_user pivot migration (user_id, branch_id, role_id)
- Update User model to add phone, is_super_admin fields
- Add branch relationships to User model
- Run migrations and test database structure

**Task 1.2: Branch Model & Relationships**
- Create Branch model with relationships
- Create BelongsToBranch trait for global scoping
- Add global scope to BelongsToBranch trait
- Test model relationships and scoping

**Task 1.3: Permission System Setup**
- Install and configure spatie/laravel-permission
- Create permission seeder (Super Admin, Branch Manager, Sales, Inventory, Accounts, Service)
- Create role seeder with default permissions
- Test permission system functionality

### **Day 2 Tasks (2-3 tasks)**

**Task 1.4: Branch Context Middleware**
- Create BranchContext middleware
- Register BranchContext middleware in kernel
- Implement branch resolution logic in middleware
- Add branch context to all authenticated routes
- Test middleware functionality

**Task 1.5: Branch Controller & Policies**
- Create BranchController with CRUD operations
- Create BranchPolicy (Super Admin only for CRUD)
- Create BranchStoreRequest and BranchUpdateRequest
- Test controller and policy functionality

**Task 1.6: User Branch Management**
- Update UserController to handle branch assignments
- Create UserBranchController for branch-user management
- Add branch scoping to existing controllers
- Test user-branch assignment functionality

### **Day 3 Tasks (2-3 tasks)**

**Task 1.7: Branch Frontend Components**
- Create Branch index page with DataTable
- Create Branch create/edit dialog
- Create branch context store (Pinia)
- Test frontend components

**Task 1.8: Branch Switcher & Navigation**
- Create Branch switcher in top navigation
- Update User management to show branch assignments
- Add branch filter to existing CRUD pages
- Test navigation and filtering

**Task 1.9: Branch Testing Suite**
- Create Branch model tests
- Create BranchController tests
- Create BranchPolicy tests
- Create BranchContext middleware tests
- Test branch scoping in existing controllers

---

## Sprint 2 - Master Data: Brands, Categories, Taxes, Units, Attributes & Variants, Products

### **Day 4 Tasks (2-3 tasks)**

**Task 2.1: Master Data Migrations**
- Create brands migration (id, name, description, logo, is_active, branch_id)
- Create categories migration (id, name, description, parent_id, is_active, branch_id)
- Create units migration (id, name, symbol, is_active, branch_id)
- Create taxes migration (id, name, rate, type, is_active, branch_id)
- Run migrations and test database structure

**Task 2.2: Attributes & Product Updates**
- Create attributes migration (id, name, type, is_active, branch_id)
- Create attribute_values migration (id, attribute_id, value, is_active)
- Update products migration to add branch_id, warranty_months, guarantee_months
- Update product_variants migration to add branch_id, cost_price, mrp
- Run migrations and test database structure

**Task 2.3: Master Data Models**
- Create Brand model with BelongsToBranch trait
- Create Category model with BelongsToBranch trait and tree relationships
- Create Unit model with BelongsToBranch trait
- Create Tax model with BelongsToBranch trait
- Test model relationships and scoping

### **Day 5 Tasks (2-3 tasks)**

**Task 2.4: Attribute Models & Product Updates**
- Create Attribute model with BelongsToBranch trait
- Create AttributeValue model
- Update Product model with new relationships and branch scoping
- Update ProductVariation model with branch scoping
- Test model relationships

**Task 2.5: Product Services Foundation**
- Create ProductVariantGenerator service
- Create SKUGenerator service
- Create BarcodeGenerator service
- Implement basic variant creation logic
- Test service functionality

**Task 2.6: Attribute Matrix Service**
- Create AttributeMatrixGenerator service
- Implement variant creation from attribute combinations
- Add uniqueness validation for SKU/barcode per branch
- Test attribute matrix generation

### **Day 6 Tasks (2-3 tasks)**

**Task 2.7: Master Data Controllers**
- Create BrandController with branch scoping
- Create CategoryController with branch scoping and tree operations
- Create UnitController with branch scoping
- Create TaxController with branch scoping
- Test controller functionality

**Task 2.8: Attribute Controllers**
- Create AttributeController with branch scoping
- Create AttributeValueController
- Update ProductController with variant generation
- Create ProductVariantController
- Test controller functionality

**Task 2.9: Master Data Policies & Requests**
- Create BrandPolicy, CategoryPolicy, UnitPolicy, TaxPolicy
- Create AttributePolicy
- Create form requests for all entities
- Test policies and validation

### **Day 7 Tasks (2-3 tasks)**

**Task 2.10: Master Data Frontend - Brands & Categories**
- Create Brand index/create/edit pages
- Create Category index/create/edit pages with tree view
- Test frontend components

**Task 2.11: Master Data Frontend - Units & Taxes**
- Create Unit index/create/edit pages
- Create Tax index/create/edit pages
- Test frontend components

**Task 2.12: Attribute Frontend Components**
- Create Attribute index/create/edit pages
- Create AttributeValue management interface
- Create attribute selection component for products
- Test frontend components

### **Day 8 Tasks (2-3 tasks)**

**Task 2.13: Product Variant Frontend**
- Update Product create/edit with variant matrix generator
- Create ProductVariant management interface
- Test product variant functionality

**Task 2.14: Media Management Setup**
- Install and configure spatie/laravel-medialibrary
- Add media relationships to Product and Brand models
- Create media upload service
- Test media functionality

**Task 2.15: Media Frontend Components**
- Create image gallery component
- Implement image resizing and optimization
- Add image upload component to products/brands
- Test media upload functionality

### **Day 9 Tasks (2-3 tasks)**

**Task 2.16: Master Data Testing Suite**
- Create Brand model and controller tests
- Create Category model and controller tests
- Create Unit, Tax, Attribute model tests
- Test branch scoping for all catalog entities

**Task 2.17: Product Service Testing**
- Create ProductVariantGenerator service tests
- Create SKU/Barcode generator tests
- Test attribute matrix generation
- Test variant creation workflows

**Task 2.18: Integration Testing & Bug Fixes**
- Test complete master data workflow
- Fix any integration issues
- Test branch scoping across all entities
- Document any issues found

---

## **Assignment Strategy:**

**For Junior Developer A (Backend Focus):**
- Days 1-3: Focus on database, models, controllers, policies
- Days 4-6: Focus on services, business logic, migrations
- Days 7-9: Focus on testing, integration, bug fixes

**For Junior Developer B (Frontend Focus):**
- Days 1-3: Focus on frontend components, UI/UX
- Days 4-6: Focus on Vue components, PrimeVue integration
- Days 7-9: Focus on testing, user experience, bug fixes

**For Junior Developer C (Full-Stack):**
- Days 1-3: Focus on API endpoints, middleware, authentication
- Days 4-6: Focus on complex business logic, integrations
- Days 7-9: Focus on testing, performance, documentation

Each task is designed to be:
- **Completable in 4-5 hours**
- **Independently testable**
- **Clear deliverables**
- **Easy to track progress**
- **Suitable for code review**
