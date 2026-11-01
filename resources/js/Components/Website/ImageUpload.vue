<script setup>
import Button from "primevue/button";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

// Pick / preview / remove one image. v-model is the File (or null); "removed" tells the parent to delete the saved one.
const props = defineProps({
    current: { type: String, default: null },
    accept: { type: String, default: "image/png,image/jpeg,image/webp" },
    hint: { type: String, default: "" },
    previewClass: { type: String, default: "h-24 w-auto" },
});
const file = defineModel({ default: null });
const removed = defineModel("removed", { type: Boolean, default: false });

const { t } = useI18n();
const input = ref();
const localUrl = ref(null);

const shown = computed(() => localUrl.value || (removed.value ? null : props.current));

function pick(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    file.value = f;
    removed.value = false;
    localUrl.value = URL.createObjectURL(f);
}
function clear() {
    file.value = null;
    localUrl.value = null;
    removed.value = !!props.current;
    if (input.value) input.value.value = "";
}
</script>

<template>
    <div class="flex items-center gap-4">
        <div class="shrink-0 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 flex items-center justify-center min-w-[6rem] min-h-[6rem]">
            <img v-if="shown" :src="shown" alt="" :class="previewClass" class="object-contain" />
            <i v-else class="pi pi-image text-3xl text-slate-300" />
        </div>
        <div class="flex flex-col gap-2">
            <div class="flex gap-2">
                <Button type="button" :label="t('website.admin.choose_image')" icon="pi pi-upload" size="small" severity="secondary" outlined @click="input.click()" />
                <Button v-if="shown" type="button" icon="pi pi-trash" size="small" severity="danger" text :aria-label="t('website.admin.remove_image')" @click="clear" />
            </div>
            <small v-if="hint" class="text-slate-500">{{ hint }}</small>
            <input ref="input" type="file" :accept="accept" class="hidden" @change="pick" />
        </div>
    </div>
</template>
