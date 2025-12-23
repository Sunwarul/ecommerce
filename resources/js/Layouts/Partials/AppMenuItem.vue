<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onBeforeMount, ref, watch } from "vue";
import { useLayout } from "./LayoutComposable";

const { layoutState, setActiveMenuItem, toggleMenu } = useLayout();

const props = defineProps({
    item: { type: Object, default: () => ({}) },
    index: { type: Number, default: 0 },
    root: { type: Boolean, default: true },
    parentItemKey: { type: String, default: null },
});

const page = usePage();
const currentUrl = computed(() => page.url); // like "/products"

const isActiveMenu = ref(false);
const itemKey = ref("");

function isItemActiveByUrl(item) {
    if (!item) return false;

    // Leaf link: compare URL
    if (item.to && !item.items) {
        // item.to is full url (Ziggy route()), example: "https://site.com/products" OR "/products"
        // normalize to pathname only
        try {
            const itemPath = new URL(item.to, window.location.origin).pathname;
            return (
                currentUrl.value === itemPath ||
                currentUrl.value.startsWith(itemPath + "/")
            );
        } catch {
            // fallback if item.to is already a relative path
            return (
                currentUrl.value === item.to ||
                currentUrl.value.startsWith(item.to + "/")
            );
        }
    }

    // Parent: any child active?
    if (item.items?.length) return item.items.some(isItemActiveByUrl);

    return false;
}

onBeforeMount(() => {
    itemKey.value = props.parentItemKey
        ? `${props.parentItemKey}-${props.index}`
        : String(props.index);

    const activeItem = layoutState.activeMenuItem;

    // ✅ fixed precedence + null-safe
    isActiveMenu.value =
        activeItem === itemKey.value ||
        (activeItem ? activeItem.startsWith(itemKey.value + "-") : false);

    // ✅ auto-open parents based on current route on first render
    if (props.item.items && isItemActiveByUrl(props.item)) {
        setActiveMenuItem(itemKey.value);
    }
});

watch(
    () => layoutState.activeMenuItem,
    (newVal) => {
        // ✅ null-safe
        isActiveMenu.value =
            newVal === itemKey.value ||
            (!!newVal && newVal.startsWith(itemKey.value + "-"));
    }
);

// ✅ update active parents when route changes (Inertia navigation)
watch(
    () => currentUrl.value,
    () => {
        if (props.item.items && isItemActiveByUrl(props.item)) {
            setActiveMenuItem(itemKey.value);
        }
    }
);

function itemClick(event, item) {
    if (item.disabled) {
        event.preventDefault();
        return;
    }

    if (
        (item.to || item.url) &&
        (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)
    ) {
        toggleMenu();
    }

    if (item.command) {
        item.command({ originalEvent: event, item });
    }

    const foundItemKey = item.items
        ? isActiveMenu.value
            ? props.parentItemKey
            : itemKey.value
        : itemKey.value;

    setActiveMenuItem(foundItemKey);
}

function checkActiveRoute(item) {
    return isItemActiveByUrl(item);
}
</script>

<template>
    <li
        :class="{
            'layout-root-menuitem': root,
            'active-menuitem': isActiveMenu,
        }"
    >
        <div
            v-if="root && item.visible !== false"
            class="layout-menuitem-root-text"
        >
            {{ item.label }}
        </div>

        <!-- Parent / toggle -->
        <a
            v-if="(!item.to || item.items) && item.visible !== false"
            :href="item.url || '#'"
            @click.prevent="itemClick($event, item)"
            :class="item.class"
            :target="item.target"
            tabindex="0"
        >
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
            <i
                class="pi pi-fw pi-angle-down layout-submenu-toggler"
                v-if="item.items"
            ></i>
        </a>

        <!-- Leaf link -->
        <Link
            v-if="item.to && !item.items && item.visible !== false"
            @click="itemClick($event, item)"
            :class="[item.class, { 'active-route': checkActiveRoute(item) }]"
            tabindex="0"
            :href="item.to"
        >
            <i :class="item.icon" class="layout-menuitem-icon"></i>
            <span class="layout-menuitem-text">{{ item.label }}</span>
        </Link>

        <Transition
            v-if="item.items && item.visible !== false"
            name="layout-submenu"
        >
            <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
                <AppMenuItem
                    v-for="(child, i) in item.items"
                    :key="`${child.label}-${i}`"
                    :index="i"
                    :item="child"
                    :parentItemKey="itemKey"
                    :root="false"
                />
            </ul>
        </Transition>
    </li>
</template>
