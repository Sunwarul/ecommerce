# Users, roles, permissions and branches

## Users

**Users & access → Users**: **Full name**, **Email** (login), phone, **Password** (leave empty when editing to keep it), **Roles**, **Branch** and photo.

## Roles and permissions

**Users & access → Roles**: give the role a name and tick its permissions. Permissions are named *Module Action*, e.g. *Product Store*, *Order Void*, *Report Export*.

Suggested roles:

| Role | Typical permissions |
|---|---|
| **Cashier** | POS Index, Order Index, Order Invoice, Order Payment, Customer Index/Store, Installment Collect |
| **Store keeper** | Product Index, Product Movement, Warehouse Index |
| **Manager** | Everything of Cashier + Order Void, Order Complete, Report Index, Report Export, Installment Create/Cancel, Product Store/Update, Expense |
| **Website editor** | Website Manage, Contact Message Index |

Name the manager role exactly **manager** (or **admin**) – those roles may also see **All branches** in Orders and Reports and edit sale dates.

Safety rules: only a Super Admin can give the Super Admin role; nobody can change their own roles; at least one Super Admin always remains; staff can only grant permissions they have themselves.

## Branches

**Organization → Branches**: name, code, address, phone, active. Staff switch with the **Branch** picker in the top bar; switching closes their open POS session. Orders, payments and installment agreements are filtered by the current branch.
