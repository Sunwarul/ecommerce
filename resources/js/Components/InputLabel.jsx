import React from 'react';

const InputLabel = ({ value, children, className = '', ...props }) => {
    return (
        <label className={`block text-sm font-medium text-gray-700 ${className}`} {...props}>
            {value ? value : children}
        </label>
    );
};

export default InputLabel;

