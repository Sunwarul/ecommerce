/**
 * Common status options for forms
 */
export const statuses = [
    { label: 'Active', value: 1 },
    { label: 'Inactive', value: 0 }
];

/**
 * Helper to get status label from value
 */
export const getStatusLabel = (value) => {
    const status = statuses.find(s => s.value === value);
    return status ? status.label : 'Unknown';
};
