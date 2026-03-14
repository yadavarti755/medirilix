# Slider Module Structure

This document outlines the code structure for the Slider module in the Medirilix application.

## 1. Routes

**File:** `routes/web.php`
**Prefix:** `/secure/sliders`
**Middleware:** `auth`, `can:view slider` (and others)

| HTTP Method | URI                            | Controller Method   | Route Name                    | Middleware           |
| :---------- | :----------------------------- | :------------------ | :---------------------------- | :------------------- |
| GET         | `/sliders`                     | `index`             | `sliders.index`               | `can:view slider`    |
| POST        | `/sliders/fetch-for-datatable` | `fetchForDatatable` | `sliders.fetch-for-datatable` | `can:view slider`    |
| POST        | `/sliders/delete/{slider}`     | `destroy`           | `sliders.destroy`             | `can:delete slider`  |
| GET         | `/sliders/create`              | `create`            | `sliders.create`              | `can:add slider`     |
| POST        | `/sliders`                     | `store`             | `sliders.store`               | `can:add slider`     |
| GET         | `/sliders/{slider}`            | `show`              | `sliders.show`                | `can:view slider`    |
| GET         | `/sliders/{slider}/edit`       | `edit`              | `sliders.edit`                | `can:edit slider`    |
| POST        | `/sliders/{slider}`            | `update`            | `sliders.update`              | `can:edit slider`    |
| POST        | `/sliders/publish/{slider}`    | `publish`           | `sliders.publish`             | `can:publish slider` |

## 2. Controller

**File:** `app/Http/Controllers/Secure/SliderController.php`
**Namespace:** `App\Http\Controllers\Secure`

**Key Dependencies:**

- `App\Services\SliderService`
- `App\Services\CategoryService`

**Key Methods:**

- `index`: Returns view `secure.sliders.index`.
- `fetchForDatatable`: Returns JSON for DataTables.
- `create`: Returns view `secure.sliders.create` with categories.
- `store`: Handles creation using `StoreSliderRequest` and `SliderDto`.
- `show`: Returns view `secure.sliders.show`.
- `edit`: Returns view `secure.sliders.edit`.
- `update`: Handles updates using `UpdateSliderRequest` and `SliderDto`.
- `destroy`: Deletes a slider.
- `publish`: Toggles publication status.

## 3. Service

**File:** `app/Services/SliderService.php`
**Namespace:** `App\Services`

**Key Dependencies:**

- `App\Repositories\SliderRepository`
- `App\Traits\FileUploadTraits`

**Key Methods:**

- `findForPublic()`
- `findAll()`
- `findById($id)`
- `create(SliderDto $sliderDto)`: Handles file upload and delegates to repository.
- `update(SliderDto $sliderDto, $id)`: Handles file upload and delegates to repository.
- `delete($id)`
- `publish(SliderDto $sliderDto, $id)`

## 4. Repository

**File:** `app/Repositories/SliderRepository.php`
**Namespace:** `App\Repositories`

**Key Methods:**

- `findForPublic()`: Returns published sliders with category.
- `findAll()`: Returns all sliders with category.
- `findById($id)`: Returns specific slider with category.
- `create($data)`: Creates a new record.
- `update($data, $id)`: Updates a record.
- `delete($id)`: Soft deletes a record.

## 5. Model

**File:** `app/Models/Slider.php`
**Namespace:** `App\Models`

**Table:** `sliders` (inferred)
**Traits:** `SoftDeletes`, `LogsActivity`

**Fillable Fields:**

- `category_id`
- `title`
- `subtitle`
- `description`
- `file_name`
- `is_published`
- `created_by`
- `updated_by`

**Accessors:**

- `is_published_desc` ("Published" / "Draft")
- `file_url` (Full URL to image)

**Relationships:**

- `category()`: BelongsTo `Category`.

## 6. DTO (Data Transfer Object)

**File:** `app/DTO/SliderDto.php`
**Namespace:** `App\DTO`

Used to transfer data between Controller and Service.
**Properties:**

- `$category_id`
- `$title`
- `$subtitle`
- `$description`
- `$file_name`
- `$is_published`
- `$created_by`
- `$updated_by`

## 7. Form Requests

**Directories:** `app/Http/Requests`

### StoreSliderRequest

**File:** `StoreSliderRequest.php`
**Rules:**

- `file_name`: required, file, mimes:jpg,jpeg,png,gif,webp, max:2048

### UpdateSliderRequest

**File:** `UpdateSliderRequest.php`
**Rules:**

- `file_name`: nullable, file, mimes:jpg,jpeg,png,gif,webp, max:2048

## 8. Views

**Directory:** `resources/views/secure/sliders`

- `index.blade.php`: List view (Datatable).
- `create.blade.php`: Create form.
- `edit.blade.php`: Edit form.
- `show.blade.php`: Detail view.
