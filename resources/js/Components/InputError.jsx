import React from 'react';

const InputError = ({ message, className = '', ...props }) => {
    return message ? (
        <div {...props}>
            <p className={`text-sm text-red-600 ${className}`}>{message}</p>
        </div>
    ) : null;
};

export default InputError;

