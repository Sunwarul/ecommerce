# E-Commerce — Laravel + React + Inertia

## 🎉 100% REACT - ALL VUE FILES REMOVED!

This project is now **pure React 18** with Inertia 2 and PrimeReact. All Vue files deleted!

### Quick Start

```bash
# Automated setup (Windows)
setup-react.bat

# Automated setup (Linux/Mac)
chmod +x setup-react.sh && ./setup-react.sh

# Manual setup
npm install
npm run dev
php artisan serve
```

### 📚 Documentation

-   **[README-REACT.md](README-REACT.md)** - Start here! Complete React guide
-   **[INSTALLATION.md](INSTALLATION.md)** - Detailed installation steps
-   **[REACT_MIGRATION_GUIDE.md](REACT_MIGRATION_GUIDE.md)** - Migration patterns & examples
-   **[CONVERSION_SUMMARY.md](CONVERSION_SUMMARY.md)** - What was changed
-   **[REACT-CHECKLIST.md](REACT-CHECKLIST.md)** - Verification checklist

### 🎯 Generate CRUD with Blueprint

```bash
# Create a blueprint YAML file, then:
php artisan blueprint:build your-file.yaml

# This generates React components automatically!
# - resources/js/Pages/YourModel/Index.jsx
# - resources/js/Pages/YourModel/Form.jsx
# Plus: Models, Controllers, Migrations, Routes
```

### ✨ Tech Stack

-   **Backend:** Laravel 12 + PHP 8.3 + MySQL 8
-   **Frontend:** React 18 + Inertia 2 + PrimeReact 10
-   **Build:** Vite 6 + Tailwind CSS
-   **Generator:** Blueprint (auto-generates React CRUD)

---

## How to use with Cursor

1. Open a new thread; mention `@workspace`.
2. Pin: `.cursorrules`, `SRS.md`, `ARCHITECTURE.md`, `DB-SCHEMA.md`, and the current sprint brief in `docs/backlog/`.
3. Start each sprint with **PROMPTS/planning.md**. Then run **PROMPTS/step-implementation.md** per step.
4. After each step, run **PROMPTS/review.md** then **PROMPTS/tests.md**.

## Sprints Provided

-   Sprint 00–03 briefs included under `docs/backlog/`.
-   Copy and adapt for later sprints.

## CI

GitHub Actions workflow included for PHP + JS lint/test.

---

_Last updated: October 2025 - React Migration Complete_
