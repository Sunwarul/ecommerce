import React, { useEffect } from 'react';
import { Dialog } from 'primereact/dialog';

const Modal = ({ show = false, maxWidth = '2xl', closeable = true, onClose, children }) => {
    useEffect(() => {
        const close = (e) => {
            if (closeable && e.keyCode === 27 && show) {
                onClose();
            }
        };

        document.addEventListener('keydown', close);

        return () => document.removeEventListener('keydown', close);
    }, [show, closeable, onClose]);

    const maxWidthClass = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[maxWidth];

    return (
        <Dialog
            visible={show}
            onHide={onClose}
            modal
            dismissableMask={closeable}
            className={maxWidthClass}
        >
            {children}
        </Dialog>
    );
};

export default Modal;

