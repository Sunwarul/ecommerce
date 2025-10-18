import React, { useState, useRef, useEffect } from 'react';
import { Transition } from '@headlessui/react';

const Dropdown = ({ align = 'right', width = '48', contentClasses = 'py-1 bg-white', trigger, children }) => {
    const [open, setOpen] = useState(false);
    const dropdownRef = useRef(null);

    const alignmentClasses = {
        left: 'origin-top-left left-0',
        right: 'origin-top-right right-0',
    }[align];

    const widthClasses = {
        48: 'w-48',
    }[width];

    useEffect(() => {
        const closeOnEscape = (e) => {
            if (open && e.key === 'Escape') {
                setOpen(false);
            }
        };

        const closeOnOutsideClick = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setOpen(false);
            }
        };

        document.addEventListener('keydown', closeOnEscape);
        document.addEventListener('click', closeOnOutsideClick);

        return () => {
            document.removeEventListener('keydown', closeOnEscape);
            document.removeEventListener('click', closeOnOutsideClick);
        };
    }, [open]);

    return (
        <div className="relative" ref={dropdownRef}>
            <div onClick={() => setOpen(!open)}>{trigger}</div>

            <Transition
                show={open}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 scale-95"
                enterTo="opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="opacity-100 scale-100"
                leaveTo="opacity-0 scale-95"
            >
                <div
                    className={`absolute z-50 mt-2 rounded-md shadow-lg ${alignmentClasses} ${widthClasses}`}
                    onClick={() => setOpen(false)}
                >
                    <div className={`rounded-md ring-1 ring-black ring-opacity-5 ${contentClasses}`}>
                        {children}
                    </div>
                </div>
            </Transition>
        </div>
    );
};

export const DropdownLink = ({ className = '', children, ...props }) => {
    return (
        <Link
            {...props}
            className={`block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out ${className}`}
        >
            {children}
        </Link>
    );
};

export default Dropdown;

