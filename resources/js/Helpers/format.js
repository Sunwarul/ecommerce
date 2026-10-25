// Shared formatting helpers for sales / reports / SMS pages.
import { reactive } from "vue";
import i18n from "@/Plugins/i18n";

export const n = (v) => Number(v || 0);

/*
 * Number display preferences from Settings > Bangla & appearance (shared as page.props.configs):
 *  - bnDigits: Bangla digits (১২,৩৪৫.০০) while the interface language is Bangla
 *  - grouping: "international" (1,234,567) or "indian" lakh/crore (12,34,567)
 */
const prefs = reactive({ bnDigits: false, grouping: "international" });

export function configureNumbers(configs = {}) {
    prefs.bnDigits = !!configs.bn_digits;
    prefs.grouping = configs.number_grouping === "indian" ? "indian" : "international";
}

const isBangla = () => i18n.global.locale.value === "bn";

export function numberLocale() {
    const base = prefs.grouping === "indian" ? "en-IN" : "en-US";
    return prefs.bnDigits && isBangla() ? `${base}-u-nu-beng` : base;
}

/** Bangla month names while the UI is Bangla; digits follow the Bangla-digits setting. */
function dateLocale() {
    if (!isBangla()) return "en-GB";
    return prefs.bnDigits ? "bn-BD" : "bn-BD-u-nu-latn";
}

export function money(v, digits = 2) {
    return n(v).toLocaleString(numberLocale(), { minimumFractionDigits: digits, maximumFractionDigits: digits });
}

/** Plain number (quantities, counts) with the same digit / grouping rules as money(). */
export function num(v, digits = 0) {
    return n(v).toLocaleString(numberLocale(), { maximumFractionDigits: digits });
}

export function compact(v) {
    return n(v).toLocaleString(numberLocale(), { notation: "compact", maximumFractionDigits: 1 });
}

export function date(d) {
    if (!d) return "-";
    const dt = d instanceof Date ? d : new Date(String(d).replace(" ", "T"));
    if (isNaN(dt)) return String(d);
    return dt.toLocaleDateString(dateLocale(), { day: "2-digit", month: "short", year: "numeric" });
}

export function dateTime(d) {
    if (!d) return "-";
    const dt = d instanceof Date ? d : new Date(String(d).replace(" ", "T"));
    if (isNaN(dt)) return String(d);
    return dt.toLocaleString(dateLocale(), { day: "2-digit", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
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
