<template>
    <q-layout view="hHh lpR fFf">
        <!-- Header -->
        <q-header elevated class="bg-secondary text-white">
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click="toggleDrawer" />
                <q-toolbar-title>{{ appName }}</q-toolbar-title>

                <q-btn-dropdown flat dense round icon="account_circle">
                    <q-list style="min-width: 200px">
                        <q-item clickable v-close-popup tag="a" href="/profile/edit">
                            <q-item-section avatar>
                                <q-icon name="person" />
                            </q-item-section>
                            <q-item-section>Mi Perfil</q-item-section>
                        </q-item>
                        <q-separator />
                        <q-item clickable v-close-popup @click="logout">
                            <q-item-section avatar>
                                <q-icon name="logout" />
                            </q-item-section>
                            <q-item-section>Cerrar Sesión</q-item-section>
                        </q-item>
                    </q-list>
                </q-btn-dropdown>
            </q-toolbar>
        </q-header>

        <!-- Drawer -->
        <q-drawer v-model="drawerOpen" side="left" bordered>
            <q-list>
                <q-item-label header class="text-secondary">Menú</q-item-label>

                <q-item clickable tag="a" href="/standard/dashboard" active-class="bg-blue-1">
                    <q-item-section avatar>
                        <q-icon name="dashboard" />
                    </q-item-section>
                    <q-item-section>Dashboard</q-item-section>
                </q-item>

                <q-item clickable tag="a" href="/standard/section1" active-class="bg-blue-1">
                    <q-item-section avatar>
                        <q-icon name="person_add" />
                    </q-item-section>
                    <q-item-section>Registrar Cursante</q-item-section>
                </q-item>

                <q-item clickable tag="a" href="/standard/section2" active-class="bg-blue-1">
                    <q-item-section avatar>
                        <q-icon name="groups" />
                    </q-item-section>
                    <q-item-section>Ver cursantes registrados</q-item-section>
                </q-item>
            </q-list>
        </q-drawer>

        <!-- Page -->
        <q-page-container>
            <slot />
        </q-page-container>
    </q-layout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const drawerOpen = ref(false);
const appName = 'TALLERES DE VERANO - CIIDEPT';

const toggleDrawer = () => {
    drawerOpen.value = !drawerOpen.value;
};

const logout = () => {
    router.post('/logout');
};
</script>
