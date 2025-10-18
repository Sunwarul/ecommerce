/**
 * Resolve image path for display
 * @param {string} path - The image path from the database
 * @returns {string} - The full URL to the image
 */
export const resolveImagePath = (path) => {
    if (!path) return null;

    // If it's already a full URL, return as is
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    // If it's a relative path, prepend the storage URL
    return `/storage/${path}`;
};

/**
 * Handle file upload and create preview
 * @param {File} file - The uploaded file
 * @param {Function} callback - Callback function that receives the preview URL
 */
export const createImagePreview = (file, callback) => {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        callback(e.target.result);
    };
    reader.readAsDataURL(file);
};
