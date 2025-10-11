# Sprint 01 — Branches & RBAC
## Goal
Multi-branch foundation; RBAC with spatie; branch selector and scoping.

## Stories
- Create/Manage branches (Super Admin)
- Assign users to branches and roles
- BranchContext middleware; BelongsToBranch trait
- Policies for per-branch access

## Data Model
- branches
- branch_user
- spatie/permission tables

## Pages
- Branch list/create/edit
- Users list + branch/role assignment
- Topbar branch switcher

## DoD
- Non-super users only see active-branch data
- Audit entries for role/permission changes
_Last updated 2025-10-11_
