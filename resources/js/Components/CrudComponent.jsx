import React, { useState, useRef, useEffect } from 'react';
import { router, usePage, Link } from '@inertiajs/react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';
import { Dialog } from 'primereact/dialog';
import { Toolbar } from 'primereact/toolbar';
import { InputText } from 'primereact/inputtext';
import { Toast } from 'primereact/toast';
import { FilterMatchMode } from 'primereact/api';
import { debounce } from 'lodash';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const CrudComponent = ({
    form,
    formWidth = '60vw',
    canEdit = true,
    canDelete = true,
    children
}) => {
    const { props } = usePage();
    const { config, items, filters: pageFilters } = props;

    const toast = useRef(null);
    const dt = useRef(null);

    const [itemDialog, setItemDialog] = useState(false);
    const [deleteItemDialog, setDeleteItemDialog] = useState(false);
    const [deleteItemsDialog, setDeleteItemsDialog] = useState(false);
    const [selectedItems, setSelectedItems] = useState(null);
    const [submitted, setSubmitted] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);
    const [filters, setFilters] = useState({
        global: { value: pageFilters?.search || null, matchMode: FilterMatchMode.CONTAINS }
    });

    const isTrashedPage = new URLSearchParams(window.location.search).get('trashed') === '1';

    const openNew = () => {
        setIsEdit(false);
        form.reset();
        setSubmitted(false);
        setItemDialog(true);
        setPhotoPreview(null);
    };

    const hideDialog = () => {
        setItemDialog(false);
        setSubmitted(false);
    };

    const saveItem = (saveAndContinue = false) => {
        setSubmitted(true);
        const data = { ...form.data };
        if (data.ids) {
            data.ids = data.ids.map(i => i.id);
        }

        form.post(config.storeRoute, {
            onSuccess: () => {
                form.reset();
                setPhotoPreview(null);
                toast.current.show({
                    severity: 'success',
                    summary: 'Successful',
                    detail: 'Successfully Created!',
                    life: 3000
                });
                if (!saveAndContinue) {
                    hideDialog();
                } else {
                    setSubmitted(false);
                }
            },
            onError: (errors) => {
                Object.entries(errors).forEach(([key, value]) => {
                    toast.current.show({
                        severity: 'error',
                        summary: 'Validation Error',
                        detail: value,
                        life: 3000
                    });
                });
            }
        });
    };

    const updateItem = () => {
        setSubmitted(true);
        const url = config.updateRoute.replace('__ID__', editingId);
        const data = { ...form.data, _method: 'put' };

        if (data.ids) {
            data.ids = data.ids.map(i => i.id);
        }

        router.post(url, data, {
            onSuccess: () => {
                hideDialog();
                setPhotoPreview(null);
                form.reset();
                toast.current.show({
                    severity: 'success',
                    summary: 'Successful',
                    detail: 'Successfully Updated!',
                    life: 3000
                });
            },
            onError: (errors) => {
                Object.entries(errors).forEach(([key, value]) => {
                    toast.current.show({
                        severity: 'error',
                        summary: 'Validation Error',
                        detail: value,
                        life: 3000
                    });
                });
            }
        });
    };

    const editItem = (item) => {
        setItemDialog(true);
        setIsEdit(true);
        setPhotoPreview(null);
        form.setData(item);
        setEditingId(item.id);
    };

    const confirmDeleteItem = (item) => {
        setDeleteItemDialog(true);
        setPhotoPreview(null);
        form.setData(item);
        setEditingId(item.id);
    };

    const deleteItem = () => {
        setDeleteItemDialog(false);
        const url = config.deleteRoute.replace('__ID__', editingId);
        router.delete(url, {
            onSuccess: () => {
                toast.current.show({
                    severity: 'error',
                    summary: 'Deleted',
                    detail: 'Successfully Deleted',
                    life: 3000
                });
            }
        });
    };

    const confirmDeleteSelected = () => {
        setDeleteItemsDialog(true);
    };

    const deleteSelectedItems = () => {
        const itemIds = selectedItems.map(c => c.id);
        setDeleteItemsDialog(false);
        setSelectedItems(null);

        router.post(config.bulkDeleteRoute, { ids: itemIds }, {
            onSuccess: () => {
                toast.current.show({
                    severity: 'error',
                    summary: 'Deleted',
                    detail: 'Selected Items Deleted',
                    life: 3000
                });
            }
        });
    };

    const handlePhotoUpload = (event) => {
        const file = event.files[0];
        form.setData('photo', file);
        const reader = new FileReader();
        reader.onload = (e) => {
            setPhotoPreview(e.target.result);
        };
        reader.readAsDataURL(file);
    };

    const debouncedSearch = debounce((value) => {
        router.get(config.indexRoute, {
            search: value,
            per_page: dt.current?.rows || 15
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['items']
        });
    }, 300);

    const handleSearch = (e) => {
        const value = e.target.value;
        setFilters({ global: { value, matchMode: FilterMatchMode.CONTAINS } });
        debouncedSearch(value);
    };

    const handlePagination = (event) => {
        router.get(config.indexRoute, {
            page: event.page + 1,
            per_page: event.rows,
            search: filters.global.value
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['items']
        });
    };

    const exportExcel = () => {
        const params = new URLSearchParams({
            search: filters.global.value || ''
        }).toString();

        const link = document.createElement('a');
        link.href = `${config.exportRoute}?${params}`;
        link.setAttribute('download', '');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        toast.current.show({
            severity: 'success',
            summary: 'Export Started',
            detail: 'Your export will download shortly.',
            life: 3000
        });
    };

    const startContent = (
        <>
            <Button
                label="Create New"
                icon="pi pi-plus"
                className="mr-5"
                onClick={openNew}
                outlined
                severity="primary"
            />
            {config.bulkDeleteRoute && selectedItems && selectedItems.length > 0 && !isTrashedPage && (
                <Button
                    label="Bulk Delete"
                    icon="pi pi-trash"
                    className="mr-2"
                    severity="danger"
                    outlined
                    onClick={confirmDeleteSelected}
                />
            )}
        </>
    );

    const endContent = (
        <>
            {config.exportRoute && (
                <Button
                    label="Export"
                    className="mx-2"
                    icon="pi pi-upload"
                    severity="secondary"
                    onClick={exportExcel}
                />
            )}
        </>
    );

    const actionBodyTemplate = (rowData) => {
        return (
            <>
                {canEdit && (
                    <Button
                        icon="pi pi-pencil"
                        outlined
                        rounded
                        className="mr-2"
                        onClick={() => editItem(rowData)}
                        disabled={isTrashedPage}
                    />
                )}
                {canDelete && (
                    <Button
                        icon="pi pi-trash"
                        outlined
                        rounded
                        severity="danger"
                        onClick={() => confirmDeleteItem(rowData)}
                        disabled={isTrashedPage}
                    />
                )}
            </>
        );
    };

    const header = (
        <div className="flex flex-wrap gap-2 items-center justify-between">
            <h1 className="text-3xl">{config.title}</h1>
            <span className="p-input-icon-left">
                <i className="pi pi-search" />
                <InputText
                    value={filters.global.value || ''}
                    onChange={handleSearch}
                    placeholder="Search..."
                />
            </span>
        </div>
    );

    return (
        <AuthenticatedLayout>
            <Toast ref={toast} />
            <div className="card">
                <Toolbar start={startContent} end={endContent} />

                <DataTable
                    ref={dt}
                    value={items?.data || []}
                    selection={selectedItems}
                    onSelectionChange={(e) => setSelectedItems(e.value)}
                    dataKey="id"
                    paginator
                    rows={15}
                    totalRecords={items?.total || 0}
                    lazy
                    onPage={handlePagination}
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    rowsPerPageOptions={[5, 10, 25]}
                    currentPageReportTemplate={`Showing {first} to {last} of {totalRecords} ${config.resource}`}
                    header={header}
                    emptyMessage={`No ${config.resource} found.`}
                >
                    {config.bulkDeleteRoute && (
                        <Column selectionMode="multiple" style={{ width: '3rem' }} />
                    )}

                    {children}

                    <Column body={actionBodyTemplate} exportable={false} style={{ minWidth: '12rem' }} />
                </DataTable>
            </div>

            <Dialog
                visible={itemDialog}
                style={{ width: formWidth }}
                header={`${config.modelRaw} Details`}
                modal
                onHide={hideDialog}
                maximizable
            >
                {React.Children.map(children, child => {
                    if (child?.type?.name === 'Form' || child?.props?.slot === 'form') {
                        return React.cloneElement(child, {
                            form,
                            submitted,
                            handlePhotoUpload,
                            photoPreview
                        });
                    }
                    return null;
                })}

                <div className="mt-3 flex justify-end gap-2">
                    <Button label="Cancel" icon="pi pi-times" text onClick={hideDialog} />
                    {!isEdit && (
                        <>
                            <Button label="Save & Continue" text icon="pi pi-check" onClick={() => saveItem(true)} />
                            <Button label="Save" icon="pi pi-check" onClick={() => saveItem(false)} />
                        </>
                    )}
                    {isEdit && (
                        <Button label="Update" icon="pi pi-check" onClick={updateItem} />
                    )}
                </div>
            </Dialog>

            <Dialog
                visible={deleteItemDialog}
                style={{ width: '450px' }}
                header="Confirm Delete"
                modal
                onHide={() => setDeleteItemDialog(false)}
            >
                <div className="flex items-center gap-4">
                    <i className="pi pi-exclamation-triangle text-3xl" />
                    <span>Are you sure you want to delete?</span>
                </div>
                <div className="mt-3 flex justify-end gap-2">
                    <Button label="No" icon="pi pi-times" text onClick={() => setDeleteItemDialog(false)} />
                    <Button label="Yes" icon="pi pi-check" onClick={deleteItem} />
                </div>
            </Dialog>

            <Dialog
                visible={deleteItemsDialog}
                style={{ width: '450px' }}
                header="Confirm Delete"
                modal
                onHide={() => setDeleteItemsDialog(false)}
            >
                <div className="flex items-center gap-4">
                    <i className="pi pi-exclamation-triangle text-3xl" />
                    <span>Are you sure you want to delete the selected items?</span>
                </div>
                <div className="mt-3 flex justify-end gap-2">
                    <Button label="No" icon="pi pi-times" text onClick={() => setDeleteItemsDialog(false)} />
                    <Button label="Yes" icon="pi pi-check" onClick={deleteSelectedItems} />
                </div>
            </Dialog>
        </AuthenticatedLayout>
    );
};

export default CrudComponent;

