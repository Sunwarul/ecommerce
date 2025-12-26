<template>
    <div class="min-h-screen bg-white">
        <!-- Navbar -->
        <header class="sticky top-0 z-50 bg-slate-50 border-b">
            <div
                class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between"
            >
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="text-xl font-semibold text-green-600">
                        SmartPOS
                    </span>
                </div>

                <!-- Login -->
                <Link href="/login">
                    <Button severity="primary" rounded> Login </Button>
                </Link>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <div
                class="max-w-7xl mx-auto px-6 py-24 grid grid-cols-1 md:grid-cols-2 gap-16 items-center"
            >
                <!-- Content -->
                <div>
                    <h1 class="text-5xl font-bold text-slate-900 leading-tight">
                        Modern POS System <br />
                        for
                        <span class="text-primary-600">Retail & Business</span>
                    </h1>

                    <p class="mt-6 text-lg text-slate-600">
                        Manage sales, inventory, customers, and reports in one
                        powerful point-of-sale platform built for growing
                        businesses.
                    </p>

                    <div class="mt-10 flex gap-4">
                        <Link href="/login">
                            <Button size="large" severity="primary">
                                Access POS
                            </Button>
                        </Link>

                        <Button size="large" severity="secondary" outlined>
                            View Features
                        </Button>
                    </div>
                </div>

                <!-- Banner Image -->
                <div class="relative">
                    <div
                        class="absolute -inset-4 bg-primary-100 rounded-3xl blur-2xl opacity-60"
                    ></div>

                    <img
                        src="../../../assets/images/pos-banner.jpg"
                        alt="POS Dashboard Preview"
                        class="relative"
                    />
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Button from "primevue/button";
</script>
