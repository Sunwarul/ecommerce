# Coding Standards

- **Eloquent-first**: prefer relationships/scopes/resources over raw queries.
- **Validation**: Form Requests for all mutations.
- **Authorization**: Policies for resources; deny by default.
- **Branch Safety**: Never accept `branch_id` from requests. Use BranchContext.
- **IDs**: ULIDs for primary keys.
- **Soft Deletes**: Default on tenant-owned entities.
- **Transactions**: Wrap multi-step domain operations.
- **Performance**: Avoid N+1; add indexes; server-side pagination.
- **Frontend**: PrimeVue DataTable for large lists; VeeValidate; Toasts; ConfirmDialog.
- **Testing**: Cover services (stock ledger, EMI, journals), policies, controllers, views.
