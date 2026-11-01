<script setup>
import { useI18n } from "vue-i18n";

defineProps({
    src: { type: String, required: true },
    address: { type: String, default: "" },
    height: { type: String, default: "h-80" },
});
const { t } = useI18n();
// The embed URL is built server-side (App\Support\Website::mapEmbedUrl) and only ever points at Google Maps
const openUrl = (src) => src.replace("&output=embed", "").replace("/maps/embed?", "/maps?");
</script>

<template>
    <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm">
        <iframe :src="src" :class="height" class="w-full border-0 block" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
            :title="t('website.contact.map_title')" />
        <a :href="openUrl(src)" target="_blank" rel="noopener"
            class="absolute bottom-3 start-3 inline-flex items-center gap-2 rounded-lg bg-white/95 text-slate-800 text-sm font-medium px-3 py-2 shadow hover:bg-white">
            <i class="pi pi-directions text-emerald-600" />{{ t('website.contact.directions') }}
        </a>
    </div>
</template>
