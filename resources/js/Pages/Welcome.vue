<script setup>
import { usePage } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const page = usePage();
const auth = page.props.auth;
</script>

<template>
    <q-layout view="hHh lpR fFf">
        <q-header elevated class="bg-primary text-white">
            <q-toolbar>
                <q-toolbar-title>Talleres de Verano</q-toolbar-title>
                <div v-if="auth.user">
                    <q-btn flat :href="route('dashboard')" label="Dashboard" />
                </div>
                <div v-else>
                    <q-btn flat :href="route('login')" label="Iniciar Sesión" class="q-mr-md" />
                </div>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <q-page class="flex items-center justify-center q-pa-lg">
                <q-card style="max-width: 600px; width: 100%;">
                    <q-card-section>
                        <div class="text-center">
                            <h1 class="text-h3 q-mb-lg">Bienvenido a Talleres de Verano v.0.1</h1>
                            <p class="text-h6 q-mb-xl text-grey-7">
                                Sistema de gestión para la inscripción, registro y administración de talleres de verano.
                            </p>

                            <div class="q-gutter-md q-mb-lg">
                                <q-btn
                                    v-if="!auth.user"
                                    :href="route('login')"
                                    color="primary"
                                    label="Iniciar Sesión"
                                    size="lg"
                                    class="full-width"
                                />
                                <q-btn
                                    v-if="auth.user"
                                    :href="route('dashboard')"
                                    color="primary"
                                    label="Ir al Dashboard"
                                    size="lg"
                                    class="full-width"
                                />
                            </div>
                        </div>
                    </q-card-section>

                    <q-separator />

                    <q-card-section>
                        <h5 class="q-my-md">Credenciales de Prueba</h5>
                        <q-list bordered separator>
                            <q-item>
                                <q-item-section>
                                    <q-item-label>Admin</q-item-label>
                                    <q-item-label caption>admin@mistarter.local / 123456789</q-item-label>
                                </q-item-section>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-item-label>Standard User</q-item-label>
                                    <q-item-label caption>standard@mistarter.local / 123456789</q-item-label>
                                </q-item-section>
                            </q-item>
                            <q-item>
                                <q-item-section>
                                    <q-item-label>Viewer</q-item-label>
                                    <q-item-label caption>viewer@mistarter.local / 123456789</q-item-label>
                                </q-item-section>
                            </q-item>
                        </q-list>
                    </q-card-section>

                    <q-card-section class="text-center text-grey-7">
                        <small>Desarrollado por Laboratorio de Asistencia Técnológica para CIIDEPT - 2026
                            [Laravel {{ laravelVersion }} • PHP {{ phpVersion }}]</small>
                    </q-card-section>
                </q-card>
            </q-page>
        </q-page-container>
    </q-layout>
</template>
