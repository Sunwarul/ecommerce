# React Migration Guide

This project has been converted from **Vue 3 + PrimeVue** to **React 18 + PrimeReact** while maintaining Laravel + Inertia backend compatibility.

## Changes Made

### 1. Package Dependencies

-   ✅ Updated `package.json` to use React ecosystem
-   ✅ Replaced `@inertiajs/vue3` with `@inertiajs/react`
-   ✅ Replaced `primevue` with `primereact`
-   ✅ Removed Vue-specific packages (Pinia, unplugin-vue-components, etc.)
-   ✅ Added React and React-DOM

### 2. Build Configuration

-   ✅ Updated `vite.config.js` to use `@vitejs/plugin-react`
-   ✅ Changed entry point from `app.js` to `app.jsx`

### 3. Application Bootstrap

-   ✅ Created `resources/js/app.jsx` with React/Inertia setup
-   ✅ Created `AppContext.jsx` for global state management (replaces Vue's AppState)
-   ✅ Configured PrimeReact provider

### 4. Blueprint Generators

-   ✅ Created `InertiaReactGenerator.php` (replaces InertiaVueGenerator)
-   ✅ Updated `config/blueprint.php` to use React generator
-   ✅ Created React component stubs (`crud.stub`, `form.stub`)

### 5. Core Components

-   ✅ Created `CrudComponent.jsx` - Main CRUD interface component
-   ✅ Created `AuthenticatedLayout.jsx` - Basic authenticated layout
-   ✅ Created `GuestLayout.jsx` - Basic guest layout
-   ✅ Created helper files (`imageHelper.js`, `enums.js`)

## Installation Steps

### 1. Install Dependencies

```bash
npm install
```

### 2. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 3. Clear Laravel Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Using Blueprint to Generate CRUD

The blueprint generator now creates React components. Here's how to use it:

### 1. Create a Blueprint YAML File

Create a file like `draft.yaml`:

```yaml
models:
    Product:
        name: string:100
        description: text nullable
        price: decimal:8,2
        stock: integer
        is_active: boolean default:true
        category_id: id foreign:categories

controllers:
    Product:
        resource: api, web
```

### 2. Generate Components

```bash
php artisan blueprint:build
```

This will generate:

-   Migration file
-   Model with relationships
-   Controller with CRUD methods
-   Form Request validators
-   React components:
    -   `resources/js/Pages/Products/Index.jsx`
    -   `resources/js/Pages/Products/Form.jsx`
-   Routes (web.php and api.php)

### 3. Generated React Component Structure

#### Index.jsx (Main CRUD Component)

```jsx
import React from "react";
import { useForm } from "@inertiajs/react";
import CrudComponent from "@/Components/CrudComponent";
import Form from "./Form";
import { Column } from "primereact/column";

const ProductIndex = () => {
    const form = useForm({
        name: "",
        description: "",
        price: 0,
        stock: 0,
        is_active: true,
        category_id: null,
    });

    return (
        <div>
            <CrudComponent form={form}>
                <Column field="name" header="Name" sortable />
                <Column field="price" header="Price" sortable />
                <Column field="stock" header="Stock" sortable />
            </CrudComponent>
        </div>
    );
};

export default ProductIndex;
```

#### Form.jsx (Form Component)

```jsx
import React from "react";
import { InputText } from "primereact/inputtext";
import { InputTextarea } from "primereact/inputtextarea";
import { InputNumber } from "primereact/inputnumber";
import { Checkbox } from "primereact/checkbox";

const ProductForm = ({ form, submitted }) => {
    return (
        <div>
            <div className="mb-4">
                <label htmlFor="name" className="block font-bold mb-2">
                    Name
                </label>
                <InputText
                    id="name"
                    value={form.data.name}
                    onChange={(e) => form.setData("name", e.target.value)}
                    className="w-full"
                    required
                />
                {form.errors.name && (
                    <small className="text-red-500">{form.errors.name}</small>
                )}
            </div>

            <div className="mb-4">
                <label htmlFor="price" className="block font-bold mb-2">
                    Price
                </label>
                <InputNumber
                    id="price"
                    value={form.data.price}
                    onValueChange={(e) => form.setData("price", e.value)}
                    className="w-full"
                    mode="currency"
                    currency="USD"
                />
                {form.errors.price && (
                    <small className="text-red-500">{form.errors.price}</small>
                )}
            </div>
        </div>
    );
};

export default ProductForm;
```

## Key Differences: Vue vs React

### Component Syntax

**Vue 3 (Before):**

```vue
<template>
    <div>
        <InputText v-model="form.name" />
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
});
</script>
```

**React (After):**

```jsx
import React from "react";
import { useForm } from "@inertiajs/react";
import { InputText } from "primereact/inputtext";

const Component = () => {
    const form = useForm({
        name: "",
    });

    return (
        <div>
            <InputText
                value={form.data.name}
                onChange={(e) => form.setData("name", e.target.value)}
            />
        </div>
    );
};

export default Component;
```

### Common PrimeReact Component Patterns

#### InputText

```jsx
<InputText
    id="name"
    value={form.data.name}
    onChange={(e) => form.setData("name", e.target.value)}
    className="w-full"
/>
```

#### InputTextarea

```jsx
<InputTextarea
    id="description"
    value={form.data.description}
    onChange={(e) => form.setData("description", e.target.value)}
    rows={3}
    className="w-full"
/>
```

#### InputNumber

```jsx
<InputNumber
    id="price"
    value={form.data.price}
    onValueChange={(e) => form.setData("price", e.value)}
    className="w-full"
/>
```

#### Checkbox

```jsx
<Checkbox
    inputId="is_active"
    checked={form.data.is_active}
    onChange={(e) => form.setData("is_active", e.checked)}
/>
```

#### Dropdown/Select

```jsx
<Dropdown
    id="category_id"
    value={form.data.category_id}
    options={categories}
    onChange={(e) => form.setData("category_id", e.value)}
    optionLabel="name"
    optionValue="id"
    placeholder="Select a category"
    className="w-full"
/>
```

#### Calendar/DatePicker

```jsx
<Calendar
    id="publish_date"
    value={form.data.publish_date}
    onChange={(e) => form.setData("publish_date", e.value)}
    showIcon
    className="w-full"
/>
```

#### FileUpload

```jsx
<FileUpload
    mode="basic"
    name="photo"
    customUpload
    onSelect={(e) => form.setData("photo", e.files[0])}
    auto
    accept="image/*"
    chooseLabel="Choose Image"
    className="w-full"
/>
```

## Migrating Existing Pages

For existing Vue pages, you'll need to manually convert them to React. Here's a checklist:

### Conversion Checklist

1. **File Extension**: Rename `.vue` to `.jsx`
2. **Template to JSX**:

    - `<template>` → Remove, return JSX directly
    - `v-model` → `value` + `onChange`
    - `v-if` → `{condition && <Component />}`
    - `v-for` → `.map()`
    - `:prop` → `prop={value}`
    - `@event` → `onEvent={handler}`
    - `class` → `className`
    - `v-show` → `style={{ display: condition ? 'block' : 'none' }}`

3. **Script Section**:

    - `<script setup>` → React functional component
    - `ref()` → `useState()`
    - `computed()` → `useMemo()` or derived state
    - `watch()` → `useEffect()`
    - `onMounted()` → `useEffect(() => {}, [])`
    - `defineProps()` → function parameters
    - `defineEmits()` → callback props

4. **Imports**:
    - `@inertiajs/vue3` → `@inertiajs/react`
    - `primevue/*` → `primereact/*`
    - `.vue` → `.jsx`

## Backend Compatibility

**No backend changes required!** The Laravel controllers, routes, and APIs remain unchanged. Inertia handles the communication between Laravel and React seamlessly.

## PrimeReact Documentation

-   [PrimeReact Components](https://primereact.org/installation/)
-   [PrimeReact DataTable](https://primereact.org/datatable/)
-   [PrimeReact Forms](https://primereact.org/inputtext/)

## Troubleshooting

### Module not found errors

```bash
npm install
npm run dev
```

### Inertia errors

```bash
php artisan config:clear
php artisan cache:clear
```

### PrimeReact styles not loading

Make sure you have imported the theme CSS in `app.jsx`:

```jsx
import "primereact/resources/themes/lara-light-indigo/theme.css";
import "primeicons/primeicons.css";
```

## Next Steps

1. ✅ Install dependencies with `npm install`
2. ✅ Run development server with `npm run dev`
3. ✅ Test blueprint generator with `php artisan blueprint:build`
4. 🔄 Manually convert existing Vue pages to React (optional)
5. 🔄 Update custom components as needed

## Support

For React programmers joining the team:

-   All backend code remains Laravel (unchanged)
-   Frontend is now React + Inertia + PrimeReact
-   Use `useForm` from Inertia for form handling
-   Use `router` from Inertia for navigation
-   Refer to this guide for common patterns

---

**Note**: The old Vue components are still in place. You can gradually migrate them or create new React pages using the blueprint generator. Both can coexist during the transition period.
