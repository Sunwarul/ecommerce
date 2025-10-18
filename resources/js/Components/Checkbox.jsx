import React from 'react';

const Checkbox = ({ checked, onChange, value, ...props }) => {
    return (
        <input
            type="checkbox"
            checked={checked}
            onChange={(e) => onChange(e.target.checked)}
            value={value}
            className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            {...props}
        />
    );
};

export default Checkbox;

