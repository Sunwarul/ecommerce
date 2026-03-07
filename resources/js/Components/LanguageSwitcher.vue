<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const page = usePage();
const menu = ref();

const localeNames = {
    en: 'English',
    bn: 'বাংলা',
    ar: 'العربية',
    es: 'Español',
    hi: 'हिन्दी',
};

const currentLocale = computed(() => {
    return page.props.locale?.current || 'en';
});

const supportedLocales = computed(() => {
    return page.props.locale?.supported || ['en', 'bn'];
});

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

const changeLocale = (newLocale) => {
    if (!supportedLocales.value.includes(newLocale) || newLocale === currentLocale.value) {
        return;
    }

    window.location.href = route('locale.setFromUrl', { locale: newLocale });
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