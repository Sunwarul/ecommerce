import { ymd } from "@/Helpers/format";

export const FREQUENCIES = [
    { label: "Monthly", value: "monthly" },
    { label: "Weekly", value: "weekly" },
    { label: "Every 2 weeks", value: "biweekly" },
    { label: "Custom (days)", value: "custom" },
];

export const INTEREST_TYPES = [
    { label: "No interest", value: "none" },
    { label: "Flat % (whole term)", value: "flat" },
    { label: "Reducing balance (annual %)", value: "reducing" },
];

export const FEE_TYPES = [
    { label: "Fixed", value: "fixed" },
    { label: "Percent %", value: "percent" },
];

export function defaultInstallmentConfig() {
    const first = new Date();
    first.setMonth(first.getMonth() + 1);
    return {
        installment_plan_id: null,
        number_of_installments: 6,
        frequency: "monthly",
        interval_days: null,
        interest_type: "none",
        interest_rate: 0,
        processing_fee_type: "fixed",
        processing_fee: 0,
        late_fee_type: "fixed",
        late_fee_amount: 0,
        grace_days: 0,
        first_due_date: first,
        customer_nid: "",
        guarantor_name: "",
        guarantor_phone: "",
        guarantor_nid: "",
        guarantor_address: "",
        notes: "",
    };
}

export function applyPlan(config, plan) {
    if (!plan) return config;
    return {
        ...config,
        installment_plan_id: plan.id,
        number_of_installments: plan.number_of_installments,
        frequency: plan.frequency,
        interval_days: plan.interval_days,
        interest_type: plan.interest_type,
        interest_rate: Number(plan.interest_rate),
        processing_fee_type: plan.processing_fee_type,
        processing_fee: Number(plan.processing_fee),
        late_fee_type: plan.late_fee_type,
        late_fee_amount: Number(plan.late_fee_amount),
        grace_days: plan.grace_days,
    };
}

/** Config -> request payload ("installment" key). */
export function installmentPayload(config) {
    return {
        enabled: true,
        ...config,
        interval_days: config.frequency === "custom" ? config.interval_days : null,
        first_due_date: ymd(config.first_due_date),
    };
}
