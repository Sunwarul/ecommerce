import React, { useRef, useImperativeHandle, forwardRef } from 'react';

const TextInput = forwardRef(({ className = '', autoFocus = false, ...props }, ref) => {
    const input = useRef();

    useImperativeHandle(ref, () => ({
        focus: () => input.current.focus(),
    }));

    React.useEffect(() => {
        if (autoFocus) {
            input.current.focus();
        }
    }, [autoFocus]);

    return (
        <input
            {...props}
            className={`rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ${className}`}
            ref={input}
        />
    );
});

export default TextInput;

