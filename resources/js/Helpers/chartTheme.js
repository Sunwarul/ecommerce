// Chart colours from the validated data-viz reference palette.
// Categorical slots are assigned in this fixed order (never cycled); status colours are reserved.
const isDark = () => document.documentElement.classList.contains("app-dark");

export const SERIES = {
    light: ["#2a78d6", "#eb6834", "#1baf7a", "#eda100", "#e87ba4", "#008300", "#6250d6", "#e34948"],
    dark: ["#3987e5", "#d95926", "#199e70", "#c98500", "#d55181", "#008300", "#9085e9", "#e66767"],
};

export const STATUS = { good: "#0ca30c", warning: "#fab219", serious: "#ec835a", critical: "#d03b3b" };

export function series(i) {
    const list = isDark() ? SERIES.dark : SERIES.light;
    return list[i % list.length];
}

/** Up to 7 named slices; the rest fold into "Other" (no generated 9th hue). */
export function foldOther(rows, labelKey, valueKey, max = 7) {
    if (rows.length <= max + 1) return rows;
    const head = rows.slice(0, max);
    const other = rows.slice(max).reduce((s, r) => s + Number(r[valueKey] || 0), 0);
    return [...head, { [labelKey]: "Other", [valueKey]: other }];
}

export function ink() {
    return isDark()
        ? { text: "#c3c2b7", muted: "#898781", grid: "#2c2c2a", surface: "#1a1a19" }
        : { text: "#52514e", muted: "#898781", grid: "#e1e0d9", surface: "#fcfcfb" };
}

export function moneyTick(v) {
    return Number(v).toLocaleString("en-US", { notation: "compact", maximumFractionDigits: 1 });
}

/** Base Chart.js options: recessive grid, single y-axis, index tooltip (crosshair-style). */
export function baseOptions({ legend = true, money = true, horizontal = false, stacked = false } = {}) {
    const c = ink();
    const valueAxis = {
        beginAtZero: true,
        stacked,
        ticks: { color: c.muted, callback: money ? moneyTick : undefined, font: { size: 11 } },
        grid: { color: c.grid, drawTicks: false },
        border: { display: false },
    };
    const catAxis = {
        stacked,
        ticks: { color: c.muted, font: { size: 11 }, maxRotation: 0, autoSkip: true },
        grid: { display: false },
        border: { color: c.grid },
    };
    return {
        maintainAspectRatio: false,
        indexAxis: horizontal ? "y" : "x",
        interaction: { mode: "index", intersect: false },
        plugins: {
            legend: { display: legend, position: "top", align: "end", labels: { color: c.text, boxWidth: 10, boxHeight: 10, usePointStyle: true } },
            tooltip: {
                callbacks: money
                    ? { label: (ctx) => `${ctx.dataset.label || ctx.label}: ${Number(ctx.parsed[horizontal ? "x" : "y"] ?? ctx.parsed).toLocaleString("en-US", { minimumFractionDigits: 2 })}` }
                    : {},
            },
        },
        scales: horizontal ? { x: valueAxis, y: catAxis } : { x: catAxis, y: valueAxis },
    };
}

export function doughnutOptions() {
    const c = ink();
    return {
        maintainAspectRatio: false,
        cutout: "62%",
        plugins: {
            legend: { position: "right", labels: { color: c.text, boxWidth: 10, boxHeight: 10, usePointStyle: true } },
            tooltip: {
                callbacks: {
                    label: (ctx) => {
                        const total = ctx.dataset.data.reduce((s, v) => s + Number(v || 0), 0) || 1;
                        return `${ctx.label}: ${Number(ctx.parsed).toLocaleString("en-US", { minimumFractionDigits: 2 })} (${((ctx.parsed / total) * 100).toFixed(1)}%)`;
                    },
                },
            },
        },
    };
}

/** Bar dataset with the mark specs: thin bars, rounded data-end, 2px surface gap. */
export function barDataset(label, data, colorIndex) {
    return {
        label,
        data,
        backgroundColor: series(colorIndex),
        borderRadius: 4,
        borderSkipped: "start",
        borderColor: ink().surface,
        borderWidth: 0,
        maxBarThickness: 28,
        categoryPercentage: 0.7,
        barPercentage: 0.85,
    };
}

export function lineDataset(label, data, colorIndex, { fill = false } = {}) {
    const color = series(colorIndex);
    return {
        label,
        data,
        borderColor: color,
        backgroundColor: fill ? color + "22" : color,
        borderWidth: 2,
        pointRadius: data.length > 40 ? 0 : 3,
        pointHoverRadius: 5,
        pointBackgroundColor: color,
        pointBorderColor: ink().surface,
        pointBorderWidth: 2,
        tension: 0.3,
        fill,
    };
}
