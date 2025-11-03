// ./Helpers/Url.js
export function resolveImagePath(path) {
    const base = import.meta.env.VITE_APP_URL || window.location.origin;
    const defaultImage = `${base}/images/default.jpeg`; // <-- your placeholder path

    if (!path) return defaultImage;

    // If full URL already
    if (/^https?:\/\//i.test(path)) return path;

    const url = `${base}/storage/${path}`.replace(/([^:]\/)\/+/g, "$1");
    return url;
}
