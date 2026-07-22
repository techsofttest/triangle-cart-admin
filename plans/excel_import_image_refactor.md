# Product Import Refactor – Optional ZIP Image Upload

## Objective

Enhance the existing product import functionality to support an **optional ZIP upload containing product images** while maintaining full backward compatibility with the current importer.

The ZIP upload is intended to simplify image management during imports. Administrators should still be able to perform Excel-only imports for routine product updates such as price, stock, GST, margin, descriptions, and categories.

---

# Scope

## Included

- Optional ZIP upload alongside the existing Excel upload.
- Automatic extraction of the uploaded ZIP into a temporary directory.
- Image matching using the filename **without extension**.
- Automatic movement of matched images into the product image directory.
- Cleanup of temporary files after import.
- Preserve all existing import functionality.

## Excluded (Future Enhancements)

The following are intentionally out of scope for this refactor:

- Gallery image import
- Duplicate image detection
- Missing image reports
- Unused image reports
- Image optimisation or resizing
- WebP conversion
- Import summary dashboard
- Background/queued image processing
- Changes to the existing Excel format

---

# Functional Requirements

## Excel Upload

- Continue using the existing Excel import implementation.
- Excel upload remains **mandatory**.
- Existing validation and import behaviour remain unchanged.

---

## Images ZIP Upload

Add an additional upload field to the existing import modal.

### Characteristics

- Optional
- Accept `.zip` files only
- Configurable maximum upload size
- Visible alongside the existing Excel upload field

If no ZIP is uploaded, the importer should behave exactly as it does today.

---

# Import Workflow

## Scenario 1 – Excel Only

Administrator uploads:

- Excel file

System behaviour:

- Execute the existing import process.
- Skip all image processing.
- Existing product images remain unchanged.

---

## Scenario 2 – Excel + ZIP

Administrator uploads:

- Excel file
- Images ZIP

System workflow:

1. Store uploaded ZIP temporarily.
2. Extract ZIP into a unique temporary directory.
3. Build an in-memory lookup of extracted images.
4. Execute the existing importer.
5. Match product images during import.
6. Move matched images into the products directory.
7. Delete all temporary files.

---

# Image Lookup

After extraction, build an in-memory lookup using filenames without extensions.

### Supported Image Formats

- jpg
- jpeg
- png
- webp
- avif
- gif

Example:

```
amul-milk.jpg
apple.webp
coke.avif
```

Lookup becomes:

```
amul-milk
apple
coke
```

The lookup key must always be the filename without its extension.

---

# Image Matching

Continue using the existing **Featured Image** column from the Excel file.

Example:

| Featured Image |
|----------------|
| amul-milk |

Import process:

```
Featured Image

amul-milk

↓

Lookup

↓

amul-milk.jpg

↓

Move

↓

storage/app/public/products
```

The database should store the final filename exactly as it exists after being moved.

---

# Image Processing Rules

## Matching Image Found

- Move the image into:

```
storage/app/public/products
```

- Update the product's existing featured image field with the new filename.

---

## Matching Image Not Found

- Skip image processing.
- Continue importing the product.
- Existing product image remains unchanged.

A missing image must **never** cause the product import to fail.

---

# Temporary Storage

ZIP extraction should occur inside a unique temporary directory.

Suggested location:

```
storage/app/temp/imports/{unique-id}/
```

Each import should generate its own temporary folder to avoid conflicts.

---

# Cleanup

Cleanup must always occur after import completion.

Delete:

- Uploaded ZIP
- Extracted files
- Temporary extraction directory

Cleanup should execute even if the import fails or throws an exception.

---

# Backward Compatibility

This refactor must preserve all existing behaviour.

Existing Excel imports should continue to work without any changes.

Administrators must still be able to perform Excel-only product updates without uploading images.

---

# Error Handling

The importer should gracefully handle:

- Invalid ZIP files
- Corrupted ZIP archives
- Empty ZIP archives
- Unsupported file types
- Missing image matches

These conditions should not prevent the Excel import from completing, except where ZIP extraction itself fails before processing.

---

# Architecture

The image processing logic should be separated from the existing importer to keep responsibilities clear.

## Product Import Action

Responsibilities:

- Receive uploaded files.
- Validate Excel and optional ZIP.
- Invoke the image import service (when a ZIP is provided).
- Execute the existing importer.
- Trigger cleanup after completion.

---

## Image Import Service

Responsibilities:

- Store uploaded ZIP temporarily.
- Extract ZIP contents.
- Build filename lookup.
- Locate matching images.
- Move matched images into the products directory.
- Clean up temporary files.

---

## Existing Importer

The existing importer should remain largely unchanged.

Responsibilities:

- Continue handling all product import logic.
- Request matching images from the image import service when available.
- Continue existing database update logic.

---

# Acceptance Criteria

- Existing Excel imports continue working without modification.
- ZIP upload is optional.
- Excel-only product updates remain fully supported.
- Images are matched using filename without extension.
- Supported image formats are detected automatically.
- Matched images are moved into the products directory.
- Existing product images remain unchanged when no matching image exists.
- Temporary ZIP and extracted files are always removed after import.
- Existing importer behaviour remains fully backward compatible.