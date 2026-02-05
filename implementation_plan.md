# Implementation Plan: Haier Product Catalogue Seeder

## Goal

To implement a robust database seeder flow for the provided Haier product catalogue, ensuring all products are inserted as distinct items with their specific attributes (Capacity, Model, Type, etc.), while respecting the existing Ecommerce schema.

## Changes Made

### 1. Schema Update (Smart Adjustment)

**File:** `database/migrations/2026_02_03_143655_create_product_variation_attributes_table.php`

- **Change:** Made `variation_id` nullable.
- **Reason:** The Ecommerce system restricts attributes to Variable Products by default. However, the Haier catalogue consists of distinct appliances (e.g., 15L Water Heater vs 30L Water Heater) which are best represented as **Simple Products**. To store their spec attributes (Capacity, Specification, etc.) without incorrectly forcing them into a "Variable Product" structure, simple products need to support attributes. This small schema change enables that "Smart" flexibility.

### 2. ProductSeeder Logic

**File:** `database/seeders/ProductSeeder.php`

- **New Method:** `seedCatalogue()`
- **Logic:**
    - **Categories:** Auto-generated from data keys (e.g., `water_heaters` -> "Water Heaters").
    - **Products:** Created as **Simple Products**.
        - Name: Standardized as "Haier [Type] [Model]" (e.g., "Haier Water Heater ES15V-SPA(U)") for professional listing.
        - SKU: Used `material` code (e.g., `GA0U7FZ00`) where available, falling back to Model-based SKU.
        - Prices: Mapped `mrp` to Base Price, `showroom_price` to Discount Price.
        - Description: Generated generic HTML list including Remarks and Specifications.
    - **Attributes:** Dynamic generation.
        - Non-standard fields (Model, Capacity, Size, Type, Specification) are automatically converted to `ProductAttribute` and attached to the product.
    - **Stock:** Initialized random stock in warehouses for immediate testing.

## Instructions for User

To apply these changes and seed the database, run:

```bash
# 1. Refresh migrations to apply the schema change (variation_id nullable)
php artisan migrate:refresh --path=database/migrations/2026_02_03_143655_create_product_variation_attributes_table.php
# OR if safe to refresh all:
# php artisan migrate:fresh --seed

# 2. Run the specific seeder
php artisan db:seed --class=ProductSeeder
```

> [!NOTE]
> The seeder checks for existing SKUs (`material` code) to prevent duplicates, so it is safe to re-run.
