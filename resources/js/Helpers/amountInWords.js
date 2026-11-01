/*
 * Amount in words for invoices, South-Asian grouping (lakh / crore) with paisa.
 *   amountInWords(125050.5)       -> "One Lakh Twenty Five Thousand Fifty and Fifty Paisa only"
 *   amountInWords(125050.5, "bn") -> "এক লক্ষ পঁচিশ হাজার পঞ্চাশ টাকা পঞ্চাশ পয়সা মাত্র"
 */
const EN_ONES = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve",
    "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
const EN_TENS = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

// Bangla numbers 0-99 are irregular words, so they are listed in full
const BN = ["", "এক", "দুই", "তিন", "চার", "পাঁচ", "ছয়", "সাত", "আট", "নয়", "দশ",
    "এগারো", "বারো", "তেরো", "চৌদ্দ", "পনেরো", "ষোলো", "সতেরো", "আঠারো", "উনিশ", "বিশ",
    "একুশ", "বাইশ", "তেইশ", "চব্বিশ", "পঁচিশ", "ছাব্বিশ", "সাতাশ", "আটাশ", "ঊনত্রিশ", "ত্রিশ",
    "একত্রিশ", "বত্রিশ", "তেত্রিশ", "চৌত্রিশ", "পঁয়ত্রিশ", "ছত্রিশ", "সাঁইত্রিশ", "আটত্রিশ", "ঊনচল্লিশ", "চল্লিশ",
    "একচল্লিশ", "বিয়াল্লিশ", "তেতাল্লিশ", "চুয়াল্লিশ", "পঁয়তাল্লিশ", "ছেচল্লিশ", "সাতচল্লিশ", "আটচল্লিশ", "ঊনপঞ্চাশ", "পঞ্চাশ",
    "একান্ন", "বায়ান্ন", "তিপ্পান্ন", "চুয়ান্ন", "পঞ্চান্ন", "ছাপ্পান্ন", "সাতান্ন", "আটান্ন", "ঊনষাট", "ষাট",
    "একষট্টি", "বাষট্টি", "তেষট্টি", "চৌষট্টি", "পঁয়ষট্টি", "ছেষট্টি", "সাতষট্টি", "আটষট্টি", "ঊনসত্তর", "সত্তর",
    "একাত্তর", "বাহাত্তর", "তিয়াত্তর", "চুয়াত্তর", "পঁচাত্তর", "ছিয়াত্তর", "সাতাত্তর", "আটাত্তর", "ঊনআশি", "আশি",
    "একাশি", "বিরাশি", "তিরাশি", "চুরাশি", "পঁচাশি", "ছিয়াশি", "সাতাশি", "আটাশি", "ঊননব্বই", "নব্বই",
    "একানব্বই", "বিরানব্বই", "তিরানব্বই", "চুরানব্বই", "পঁচানব্বই", "ছিয়ানব্বই", "সাতানব্বই", "আটানব্বই", "নিরানব্বই"];

function en(num) {
    if (num === 0) return "";
    if (num < 20) return EN_ONES[num];
    if (num < 100) return `${EN_TENS[Math.floor(num / 10)]} ${EN_ONES[num % 10]}`.trim();
    if (num < 1000) return `${EN_ONES[Math.floor(num / 100)]} Hundred ${en(num % 100)}`.trim();
    if (num < 100000) return `${en(Math.floor(num / 1000))} Thousand ${en(num % 1000)}`.trim();
    if (num < 10000000) return `${en(Math.floor(num / 100000))} Lakh ${en(num % 100000)}`.trim();
    return `${en(Math.floor(num / 10000000))} Crore ${en(num % 10000000)}`.trim();
}

function bn(num) {
    if (num === 0) return "";
    if (num < 100) return BN[num];
    if (num < 1000) return `${BN[Math.floor(num / 100)]} শত ${bn(num % 100)}`.trim();
    if (num < 100000) return `${bn(Math.floor(num / 1000))} হাজার ${bn(num % 1000)}`.trim();
    if (num < 10000000) return `${bn(Math.floor(num / 100000))} লক্ষ ${bn(num % 100000)}`.trim();
    return `${bn(Math.floor(num / 10000000))} কোটি ${bn(num % 10000000)}`.trim();
}

export function amountInWords(value, locale = "en") {
    const cents = Math.round(Math.abs(Number(value || 0)) * 100);
    const whole = Math.floor(cents / 100);
    const paisa = cents % 100;

    if (locale === "bn") {
        if (!whole && !paisa) return "শূন্য টাকা মাত্র";
        return `${whole ? `${bn(whole)} টাকা` : ""}${paisa ? ` ${bn(paisa)} পয়সা` : ""} মাত্র`.trim();
    }
    if (!whole && !paisa) return "Zero only";
    return `${en(whole) || "Zero"}${paisa ? ` and ${en(paisa)} Paisa` : ""} only`;
}
