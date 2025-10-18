import { useState, useRef } from 'react';
import { router, useForm } from '@inertiajs/react';

export const useCrud = (initialForm = {}) => {
    const [itemDialog, setItemDialog] = useState(false);
    const [deleteItemDialog, setDeleteItemDialog] = useState(false);
    const [deleteItemsDialog, setDeleteItemsDialog] = useState(false);
    const [selectedItems, setSelectedItems] = useState(null);
    const [submitted, setSubmitted] = useState(false);
    const [isEdit, setIsEdit] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);

    const toast = useRef(null);
    const dt = useRef(null);

    const form = useForm(initialForm);

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

    const saveItem = (config, saveAndContinue = false) => {
        setSubmitted(true);
        const data = { ...form.data };
        if (data.ids) {
            data.ids = data.ids.map(i => i.id);
        }

        form.post(config.storeRoute, {
            onSuccess: () => {
                form.reset();
                setPhotoPreview(null);
                toast.current?.show({
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
                    toast.current?.show({
                        severity: 'error',
                        summary: 'Validation Error',
                        detail: value,
                        life: 3000
                    });
                });
            }
        });
    };

    const updateItem = (config) => {
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
                toast.current?.show({
                    severity: 'success',
                    summary: 'Successful',
                    detail: 'Successfully Updated!',
                    life: 3000
                });
            },
            onError: (errors) => {
                Object.entries(errors).forEach(([key, value]) => {
                    toast.current?.show({
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

    const deleteItem = (config) => {
        setDeleteItemDialog(false);
        const url = config.deleteRoute.replace('__ID__', editingId);
        router.delete(url, {
            onSuccess: () => {
                toast.current?.show({
                    severity: 'error',
                    summary: 'Deleted',
                    detail: 'Successfully Deleted',
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

    return {
        // State
        itemDialog,
        deleteItemDialog,
        deleteItemsDialog,
        selectedItems,
        submitted,
        isEdit,
        editingId,
        photoPreview,
        toast,
        dt,
        form,

        // Setters
        setItemDialog,
        setDeleteItemDialog,
        setDeleteItemsDialog,
        setSelectedItems,
        setSubmitted,
        setIsEdit,
        setEditingId,
        setPhotoPreview,

        // Actions
        openNew,
        hideDialog,
        saveItem,
        updateItem,
        editItem,
        confirmDeleteItem,
        deleteItem,
        handlePhotoUpload
    };
};

