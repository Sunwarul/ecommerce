@workspace
Implement step <n> from the approved plan. Only those files.
Generate:
- Migrations (ULIDs, soft deletes, FKs, indexes)
- Models (relations, casts, scopes/traits)
- Form Requests & Policies
- Controllers/Services
- Routes
- Inertia pages/components (PrimeVue DataTable for index; Dialog for create/edit)
- Seeders if needed

Rules:
- Do not accept branch_id from the client. Use BranchContext.
- All multi-step operations in DB transactions.
- Add meaningful docblocks.
