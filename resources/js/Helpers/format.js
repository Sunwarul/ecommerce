// Shared formatting helpers for sales / reports / SMS pages.
export const n = (v) => Number(v || 0);

export function money(v, digits = 2) {
    return n(v).toLocaleString("en-US", { minimumFractionDigits: digits, maximumFractionDigits: digits });
}

export function compact(v) {
    return n(v).toLocaleString("en-US", { notation: "compact", maximumFractionDigits: 1 });
}

export function date(d) {
    if (!d) return "-";
    const dt = d instanceof Date ? d : new Date(String(d).replace(" ", "T"));
    if (isNaN(dt)) return String(d);
    return dt.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
}

export function dateTime(d) {
    if (!d) return "-";
    const dt = d instanceof Date ? d : new Date(String(d).replace(" ", "T"));
    if (isNaN(dt)) return String(d);
    return dt.toLocaleString("en-GB", { day: "2-digit", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
}

/** Date -> "YYYY-MM-DD" in local time (never toISOString, which shifts to UTC). */
export function ymd(d) {
    if (!d) return undefined;
    const dt = d instanceof Date ? d : new Date(d);
    return `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, "0")}-${String(dt.getDate()).padStart(2, "0")}`;
}

/** Date -> "YYYY-MM-DD HH:mm:ss" in local time. */
export function ymdhms(d) {
    const dt = d instanceof Date ? d : new Date(d);
    const p = (x) => String(x).padStart(2, "0");
    return `${ymd(dt)} ${p(dt.getHours())}:${p(dt.getMinutes())}:${p(dt.getSeconds())}`;
}

export function titleCase(s) {
    return String(s || "").replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

/** Drop empty values so query strings stay clean. */
export function cleanParams(obj) {
    return Object.fromEntries(Object.entries(obj).filter(([, v]) => v !== null && v !== undefined && v !== "" && v !== false));
}

/** First validation error message from an Inertia errors bag. */
export function firstError(errors) {
    const v = Object.values(errors || {})[0];
    return Array.isArray(v) ? v[0] : v;
}
