<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { useLocale } from '@/Composables/useLocale';

const page = usePage();
const menu = ref();
const { changeLocale, currentLocale, supportedLocales } = useLocale();

const localeNames = {
    en: 'English',
    bn: 'বাংলা',
    ar: 'العربية',
    es: 'Español',
    hi: 'हिन्दी',
};

const menuItems = computed(() => 
    supportedLocales.value.map(loc => ({
        label: localeNames[loc] || loc,
        command: () => changeLocale(loc),
        class: loc === currentLocale.value ? 'bg-indigo-50 font-semibold' : '',
    }))
);

const toggleMenu = (event) => {
    menu.value.toggle(event);
};
</script>

<template>
    <div class="relative">
        <Button 
            type="button" 
            :label="localeNames[currentLocale] || currentLocale" 
            icon="pi pi-globe" 
            class="layout-topbar-action1" 
            variant="outlined"
            @click="toggleMenu" 
        />
        <Menu ref="menu" :model="menuItems" :popup="true" />
    </div>
</template>