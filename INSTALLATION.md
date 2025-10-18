# React Migration Installation Guide

## Quick Start

Follow these steps to complete the React migration:

### Step 1: Install Node Dependencies

```bash
npm install
```

This will install:

-   React 18.3.1
-   React-DOM 18.3.1
-   @inertiajs/react 2.0.0
-   PrimeReact 10.8.4
-   Other required dependencies

### Step 2: Remove Old Node Modules (Optional but Recommended)

If you encounter any issues, try a clean install:

```bash
# Windows
rmdir /s /q node_modules
del package-lock.json
npm install

# Linux/Mac
rm -rf node_modules
rm package-lock.json
npm install
```

### Step 3: Build Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### Step 4: Clear Laravel Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 5: Test the Blueprint Generator

Use the example blueprint file provided:

```bash
php artisan blueprint:build example-blueprint-test.yaml
```

This will generate:

-   `app/Models/TestItem.php`
-   `app/Http/Controllers/TestItemController.php`
-   `app/Http/Requests/StoreTestItemRequest.php`
-   `app/Http/Requests/UpdateTestItemRequest.php`
-   `database/migrations/xxxx_create_test_items_table.php`
-   `resources/js/Pages/TestItems/Index.jsx` ✨ (React Component!)
-   `resources/js/Pages/TestItems/Form.jsx` ✨ (React Component!)
-   Routes in `routes/web.php`

### Step 6: Run Migrations

```bash
php artisan migrate
```

### Step 7: Add Route (if needed)

The blueprint should auto-generate routes, but verify in `routes/web.php`:

```php
use App\Http\Controllers\TestItemController;

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::resource('test-items', TestItemController::class);
});
```

### Step 8: Start Development Server

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (in a new terminal)
npm run dev
```

Visit: `http://localhost:8000/admin/test-items`

## Verification Checklist

After installation, verify:

-   [ ] `node_modules` contains `react`, `react-dom`, `primereact`
-   [ ] `npm run dev` starts without errors
-   [ ] Browser console shows no module errors
-   [ ] PrimeReact components load correctly
-   [ ] `php artisan blueprint:build` generates `.jsx` files (not `.vue`)

## Common Issues & Solutions

### Issue: "Cannot find module 'react'"

**Solution:**

```bash
rm -rf node_modules package-lock.json
npm install
```

### Issue: "Vite manifest not found"

**Solution:**

```bash
npm run build
# or keep dev server running
npm run dev
```

### Issue: "@inertiajs/vue3 not found" in browser console

**Solution:** Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)

### Issue: Blueprint generates .vue files instead of .jsx

**Solution:** Make sure `config/blueprint.php` has:

```php
'react' => InertiaReactGenerator::class,
```

Then clear config:

```bash
php artisan config:clear
```

### Issue: PrimeReact styles not applying

**Solution:** Check `resources/js/app.jsx` includes:

```jsx
import "primereact/resources/themes/lara-light-indigo/theme.css";
import "primeicons/primeicons.css";
```

## Testing the Migration

### Test 1: Generate a Simple CRUD

```bash
php artisan blueprint:build example-blueprint-test.yaml
php artisan migrate
```

### Test 2: Verify Generated Files

Check that these React files were created:

-   `resources/js/Pages/TestItems/Index.jsx`
-   `resources/js/Pages/TestItems/Form.jsx`

### Test 3: Access in Browser

1. Start servers: `php artisan serve` and `npm run dev`
2. Visit: `http://localhost:8000/admin/test-items`
3. You should see the PrimeReact DataTable
4. Click "Create New" to test the form dialog

## Package Versions

The migration uses these versions:

### Core

-   React: 18.3.1
-   React-DOM: 18.3.1
-   @inertiajs/react: 2.0.0

### UI Library

-   PrimeReact: 10.8.4
-   PrimeIcons: 7.0.0

### Build Tools

-   Vite: 6.0
-   @vitejs/plugin-react: 4.3.3

### Utilities

-   Lodash: 4.17.21
-   Axios: 1.12.2
-   Chart.js: 4.4.7
-   Quill: 2.0.3

## Directory Structure

After migration, your structure should be:

```
resources/js/
├── app.jsx                      # Main entry (React)
├── bootstrap.js
├── Components/
│   ├── CrudComponent.jsx        # Main CRUD component (React)
│   └── ...                      # Other components (need conversion)
├── Layouts/
│   ├── AuthenticatedLayout.jsx  # Basic layout (React)
│   ├── GuestLayout.jsx          # Guest layout (React)
│   └── ...                      # Other layouts (need conversion)
├── Helpers/
│   ├── AppContext.jsx           # React Context
│   ├── imageHelper.js
│   ├── enums.js
│   └── ...
└── Pages/
    ├── TestItems/               # Example generated CRUD
    │   ├── Index.jsx            # React component
    │   └── Form.jsx             # React component
    └── ...                      # Other pages (need conversion)
```

## Next Steps

1. ✅ Complete installation steps above
2. ✅ Test blueprint generator
3. ✅ Verify React components render correctly
4. 🔄 Gradually convert existing Vue pages to React (optional)
5. 🔄 Train your React developer on Laravel/Inertia patterns

## Resources

-   [Inertia.js React Docs](https://inertiajs.com/client-side-setup#react)
-   [PrimeReact Documentation](https://primereact.org/)
-   [React Documentation](https://react.dev/)
-   [Blueprint Documentation](https://blueprint.laravelshift.com/)

---

**🎉 Migration Complete!** Your Laravel project now uses React + Inertia + PrimeReact. The blueprint generator will create React components automatically.
