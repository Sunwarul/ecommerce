<script setup>
import { computed, ref } from 'vue';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { useLocale } from '@/Composables/useLocale';
import { LOCALE_NAMES } from '@/Stores/localeStore';

defineProps({
    compact: { type: Boolean, default: false }, // icon only
});

const menu = ref();
const { changeLocale, currentLocale, supportedLocales } = useLocale();

const menuItems = computed(() =>
    supportedLocales.value.map((loc) => ({
        label: LOCALE_NAMES[loc] || loc,
        icon: loc === currentLocale.value ? 'pi pi-check' : 'pi pi-fw',
        command: () => changeLocale(loc),
        class: loc === currentLocale.value ? 'font-semibold' : '',
    }))
);
</script>

<template>
    <div class="relative">
        <Button type="button" :label="compact ? undefined : LOCALE_NAMES[currentLocale] || currentLocale" icon="pi pi-globe"
            severity="secondary" :text="compact" :outlined="!compact" size="small" :rounded="compact"
            :aria-label="LOCALE_NAMES[currentLocale]" aria-haspopup="true" @click="menu.toggle($event)" />
        <Menu ref="menu" :model="menuItems" :popup="true" />
    </div>
</template>
