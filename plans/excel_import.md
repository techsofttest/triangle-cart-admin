# TriangleCart Product Import Implementation Specification

## Objective

Implement a robust, queueable Excel product import system using **Filament 5 Importer** as the import engine while delegating all business logic to dedicated services.

The importer should support:

* Product creation and updates
* Variant creation and updates
* Automatic Brand creation
* Automatic Category creation
* Automatic Subcategory creation
* Automatic image detection
* Automatic selling price calculation
* Import validation
* Queue processing
* Failed row reporting
* Repeatable imports

The implementation must follow SOLID principles and keep business logic outside the Filament Importer.

---

# Architecture

```
Filament Product Importer
        │
        ▼
ProductImportService
        │
        ├── BrandResolver
        ├── CategoryResolver
        ├── ProductResolver
        ├── VariantResolver
        ├── ImageResolver
        ├── PriceCalculator
        └── ImportLogger
```

The Filament Importer should only orchestrate the import.

All business logic belongs inside dedicated services.

---

# Components

## Filament

Create

```
ProductImporter
```

Responsibilities

* Upload Excel
* Read rows
* Queue import
* Validate required fields
* Pass row data to ProductImportService
* Report failures

The importer should remain lightweight.

---

## ProductImportService

This is the central coordinator.

Responsibilities

* Validate row
* Resolve Brand
* Resolve Category
* Resolve Subcategory
* Resolve Product
* Resolve Variant
* Resolve Images
* Calculate Selling Price
* Save Product
* Save Variant
* Log warnings

The importer should call only this service.

---

# Excel Template

| Column              | Action            |
| ------------------- | ----------------- |
| Product SKU         | Product lookup    |
| Product Name        | Product name      |
| Brand               | Create if missing |
| Category            | Parent category   |
| Sub Category        | Child category    |
| Variant SKU         | Variant lookup    |
| Unit                | Store             |
| Size                | Store             |
| Buying Price        | Store             |
| GST                 | Store percentage  |
| Margin %            | Store             |
| Selling Price       | Ignore completely |
| Stock               | Store             |
| Key Features        | Store             |
| Product Description | Store             |
| Expiry Date         | Store             |
| Featured Image      | Lookup image      |
| Additional Images   | Lookup images     |
| Featured            | Boolean           |
| SEO Title           | Store             |
| SEO Description     | Store             |
| Courier             | Delivery flag     |

---

# Product Resolution

Products must be resolved using

```
Product SKU
```

If product exists

* Update

Else

* Create

Duplicate products must never be created.

---

# Variant Resolution

Variants must be resolved using

```
Variant SKU
```

If variant exists

Update

Otherwise

Create

Each variant stores

* SKU
* Unit
* Size
* Buying Price
* Margin
* Selling Price
* Stock

---

# Brand Resolution

Lookup Brand by name.

If Brand does not exist

Create

Required fields

* name
* slug

Cache newly created brands during import.

---

# Category Resolution

The Category column represents the parent category.

Lookup by name.

If missing

Create.

Cache results.

---

# Subcategory Resolution

The Sub Category column represents a child category.

Lookup using

* parent_id
* name

If missing

Create

```
parent_id = Parent Category
```

Cache results.

---

# Price Calculation

The Excel Selling Price column must never be imported.

Selling price is always calculated.

Formula

```
price_after_gst = buying_price × (1 + gst / 100)

selling_price = price_after_gst × (1 + margin / 100)
```

Round to two decimal places.

Examples

Buying Price

```
5.896
```

GST

```
10
```

Margin

```
40
```

Selling Price

```
9.08
```

---

# GST

Excel

```
10%
```

Convert to

```
10
```

Store

```
products.tax_percentage
```

---

# Delivery Rules

Every imported product must have

```
requires_direct_delivery = true
```

Courier column controls

```
allows_courier
```

Accepted TRUE values

* y
* yes
* true
* 1

Everything else

```
false
```

Examples

| Courier | Direct Delivery | Courier |
| ------- | --------------- | ------- |
| blank   | true            | false   |
| n       | true            | false   |
| y       | true            | true    |

---

# Featured Product

Accepted TRUE values

* y
* yes
* true
* 1

Otherwise

false.

---

# Image Resolution

Excel contains image names without file extensions.

Example

```
CM0001
```

ImageResolver should search in the configured image directory.

Search order

```
.webp

.png

.jpg

.jpeg

.avif

.gif
```

The first existing file should be used.

If no image exists

* continue import
* log warning

Do not fail the import.

---

# Additional Images

Additional Images column contains comma-separated image names.

Example

```
CM0001-1,CM0001-2,CM0001-back
```

Each image should

* perform extension lookup
* attach to Product Images table
* ignore missing images
* log warning

When updating

* remove old gallery images
* recreate from import

---

# Validation

Required fields

* Product SKU
* Product Name
* Brand
* Category
* Sub Category
* Variant SKU
* Buying Price

Stock defaults to

```
0
```

Expiry Date

nullable

Description

nullable

SEO

nullable

---

# Duplicate Behaviour

Duplicate Product SKU

Update Product

Duplicate Variant SKU

Update Variant

Duplicate Brand

Reuse

Duplicate Category

Reuse

Duplicate Subcategory

Reuse

Duplicate Images

Replace

---

# Performance Optimisation

Before importing

Load into memory

* Brands
* Categories
* Products
* Variants

Maintain runtime caches

```
Brand Cache

Category Cache

Product Cache

Variant Cache
```

Whenever a new record is created

Update the cache immediately.

Avoid repeated database queries.

---

# Queue Processing

The import must run using Laravel queues.

Flow

```
Upload

↓

Queue

↓

Process Rows

↓

Update Progress

↓

Notify User

↓

Generate Summary
```

---

# Logging

Log

* Missing images
* Invalid categories
* Validation failures
* Duplicate warnings
* Processing exceptions

Warnings should not stop the import.

Unexpected exceptions should fail only the current row.

---

# Import Summary

Display

* Total Rows
* Imported Products
* Updated Products
* Imported Variants
* Updated Variants
* New Brands
* New Categories
* New Subcategories
* Missing Images
* Failed Rows
* Processing Time

---

# Service Responsibilities

## ProductImporter

Responsible only for

* Reading Excel
* Queueing
* Validation
* Calling ProductImportService

No business logic.

---

## ProductImportService

Coordinates the complete import process.

No database-specific logic outside of resolver services.

---

## BrandResolver

Find or create Brand.

---

## CategoryResolver

Find or create Category and Subcategory.

---

## ProductResolver

Create or update Product.

---

## VariantResolver

Create or update Product Variant.

---

## ImageResolver

Resolve image filenames.

Find existing files.

Attach featured image.

Attach gallery images.

---

## PriceCalculator

Responsible only for

* GST calculation
* Margin calculation
* Selling Price calculation

No database interaction.

---

## ImportLogger

Collect

* Warnings
* Errors
* Statistics

Provide import summary after completion.

---

# Future Expansion

The architecture should support future enhancements without changing the importer.

Possible future enhancements

* Supplier-specific templates
* Stock-only imports
* Price-only imports
* Warehouse stock imports
* Automatic ZIP image extraction
* Scheduled imports
* Supplier API synchronisation
* Dry-run validation mode
* Rollback support
* Supplier mapping profiles

---

# Success Criteria

The implementation is complete when:

* Filament Importer is used as the orchestration layer.
* All business logic resides in dedicated services.
* Products and variants are updated by SKU.
* Brands, categories, and subcategories are automatically created.
* Selling prices are calculated automatically.
* Images are resolved by filename with extension lookup.
* Imports are repeatable without creating duplicates.
* Large imports are processed through queues.
* Failed rows do not stop the import.
* A detailed import summary is generated after completion.
