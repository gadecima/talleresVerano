<template>
    <StandardLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col">
                    <h4 class="q-my-none">Bienvenido, {{ user.name }}</h4>
                    <p class="text-subtitle2 text-grey">Dashboard de Usuario Estándar</p>
                </div>
            </div>

            <q-card class="q-mt-lg">
                <q-card-section>
                    <div class="text-h6 q-mb-md">Información General</div>
                    <q-list>
                        <q-item>
                            <q-item-section>
                                <q-item-label>Nombre</q-item-label>
                                <q-item-label caption>{{ user.name }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>
                                <q-item-label>Email</q-item-label>
                                <q-item-label caption>{{ user.email }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>
                                <q-item-label>Rol</q-item-label>
                                <q-item-label caption>{{ user.role.name }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item>
                            <q-item-section>
                                <q-item-label>Registrado desde</q-item-label>
                                <q-item-label caption>{{ formatDate(user.created_at) }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-list>
                </q-card-section>
            </q-card>

            <div class="q-mt-lg row q-gutter-md">
                <q-card class="col-12 col-md-6">
                    <q-card-section>
                        <div class="text-h6">Acciones Disponibles</div>
                    </q-card-section>
                    <q-card-section>
                        <q-btn
                            color="primary"
                            label="Ir a Sección 1"
                            to="/section1"
                            class="full-width q-mb-md"
                        />
                        <q-btn
                            color="primary"
                            label="Ir a Sección 2"
                            to="/section2"
                            class="full-width"
                        />
                    </q-card-section>
                </q-card>
            </div>
        </q-page>
    </StandardLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import StandardLayout from '../../Layouts/StandardLayout.vue';

defineProps({
    user: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};
</script>
