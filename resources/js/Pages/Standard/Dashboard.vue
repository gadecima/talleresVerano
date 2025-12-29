<template>
    <StandardLayout>
        <q-page class="q-pa-md">
            <div class="row items-center">
                <div class="col">
                    <h4 class="q-my-none">Bienvenido, {{ user.name }}</h4>
                    <p class="text-subtitle2 text-grey">Dashboard de Usuario Estándar</p>
                </div>
            </div>

            <!-- Acceso rápido: Registrar nuevo cursante -->
            <q-card class="q-mt-lg">
                <q-card-section class="row items-center justify-between">
                    <div class="text-h6">Registro de Cursante</div>
                        <q-btn color="primary" icon="person_add" label="Registrar nuevo cursante" href="/standard/section1" />
                        <q-btn color="secondary" icon="groups" label="Cursantes registrados" href="/standard/section2" />
                </q-card-section>
            </q-card>

            <!-- Búsqueda de cursante por DNI y asignación de talleres -->
            <q-card class="q-mt-lg">
                <q-card-section>
                    <div class="row q-col-gutter-md items-start">
                        <div class="col-12 col-md-5">
                            <div class="text-h6 q-mb-md">Buscar Cursante por DNI</div>
                            <q-input v-model="dni" label="DNI" outlined @keyup.enter="buscarCursante"/>
                            <div class="q-mt-sm">
                                <q-btn color="primary" label="Buscar" @click="buscarCursante" :loading="loading.buscar"/>
                            </div>

                            <div v-if="cursante" class="q-mt-lg">
                                <div class="text-subtitle1">Cursante</div>
                                <q-list bordered>
                                    <q-item>
                                        <q-item-section>
                                            <q-item-label>Nombre y Apellido</q-item-label>
                                            <q-item-label caption>{{ cursante.nombre_apellido }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-item>
                                        <q-item-section>
                                            <q-item-label>DNI</q-item-label>
                                            <q-item-label caption>{{ cursante.dni }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                    <q-item>
                                        <q-item-section>
                                            <q-item-label>Nivel</q-item-label>
                                            <q-item-label caption>{{ cursante.nivel_educativo }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>

                        <div class="col-12 col-md-7">
                            <div class="row items-center">
                                <div class="col-auto">
                                    <div class="text-h6">Talleres disponibles hoy ({{ diaHoy }})</div>
                                </div>
                                <div class="col">
                                    <q-btn flat icon="refresh" label="Actualizar" @click="cargarTalleresHoy" :loading="loading.talleres" class="q-ml-sm"/>
                                </div>
                            </div>

                            <q-banner v-if="talleresHoy.length === 0" class="q-my-md" dense>
                                No hay talleres disponibles para hoy.
                            </q-banner>

                            <q-list v-else bordered class="q-mt-md">
                                <q-item v-for="t in talleresHoy" :key="t.id">
                                    <q-item-section>
                                        <q-item-label>{{ t.nombre }}</q-item-label>
                                        <q-item-label caption>
                                            Responsable: {{ t.responsable }} · Orientado: {{ t.orientado }}
                                        </q-item-label>
                                    </q-item-section>
                                    <q-item-section side>
                                        <q-btn color="secondary" label="Inscribir" :disable="!cursante" @click="inscribir(t.id)"/>
                                    </q-item-section>
                                </q-item>
                            </q-list>

                            <div v-if="cursante" class="q-mt-lg">
                                <div class="text-subtitle1">Inscripciones de hoy del cursante</div>
                                <q-list bordered>
                                    <q-item v-for="ins in inscripcionesCursante" :key="ins.id">
                                        <q-item-section>
                                            <q-item-label>{{ ins.taller.nombre }}</q-item-label>
                                            <q-item-label caption>{{ formatDate(ins.fecha) }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>

            <!-- Inscripciones del día (todos los cursantes) -->
            <q-card class="q-mt-lg">
                <q-card-section>
                    <div class="row items-center">
                        <div class="col">
                            <div class="text-h6">Inscripciones de hoy ({{ formatDate(hoyFecha) }})</div>
                        </div>
                        <div class="col-auto">
                            <q-btn flat icon="refresh" label="Actualizar" @click="cargarInscripcionesHoy" :loading="loading.inscripciones"/>
                        </div>
                    </div>
                    <q-table
                        :rows="inscripcionesHoy"
                        :columns="columns"
                        row-key="id"
                        dense
                        flat
                        class="q-mt-md"
                    />
                </q-card-section>
            </q-card>
        </q-page>
    </StandardLayout>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import StandardLayout from '../../Layouts/StandardLayout.vue';

defineProps({
    user: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};

const $q = useQuasar();

const hoyFecha = ref(new Date());
const diaHoy = ref(new Date().toLocaleDateString('es-ES', { weekday: 'long' }));

// Estado y datos
const dni = ref('');
const cursante = ref(null);
const talleresHoy = ref([]);
const inscripcionesCursante = ref([]);
const inscripcionesHoy = ref([]);

const loading = ref({ buscar: false, talleres: false, inscripciones: false, inscribir: false });

const columns = [
  { name: 'cursante', label: 'Cursante', field: row => row.cursante.nombre_apellido, align: 'left' },
  { name: 'dni', label: 'DNI', field: row => row.cursante.dni, align: 'left' },
  { name: 'taller', label: 'Taller', field: row => row.taller.nombre, align: 'left' },
  { name: 'orientado', label: 'Nivel', field: row => row.taller.orientado, align: 'left' },
  { name: 'hora', label: 'Hora', field: row => new Date(row.created_at).toLocaleTimeString('es-ES'), align: 'left' },
];

function buscarCursante() {
  if (!dni.value) return;
  loading.value.buscar = true;
  window.axios.get(`/standard/cursantes/buscar/${encodeURIComponent(dni.value)}`)
    .then(res => {
      cursante.value = res.data.cursante;
      inscripcionesCursante.value = res.data.inscripciones || [];
    })
    .catch(err => {
      cursante.value = null;
      inscripcionesCursante.value = [];
      $q.notify({ type: 'negative', message: err.response?.data?.message || 'Error al buscar cursante' });
    })
    .finally(() => {
      loading.value.buscar = false;
    });
}

function cargarTalleresHoy() {
  loading.value.talleres = true;
  window.axios.get('/standard/talleres/hoy')
    .then(res => {
      talleresHoy.value = res.data.talleres || [];
      hoyFecha.value = new Date(res.data.fecha);
      diaHoy.value = res.data.dia || diaHoy.value;
    })
    .finally(() => {
      loading.value.talleres = false;
    });
}

function cargarInscripcionesHoy() {
  loading.value.inscripciones = true;
  window.axios.get('/standard/inscripciones/hoy')
    .then(res => {
      inscripcionesHoy.value = res.data.inscripciones || [];
      hoyFecha.value = new Date(res.data.fecha);
    })
    .finally(() => {
      loading.value.inscripciones = false;
    });
}

function inscribir(tallerId) {
  if (!cursante.value) return;
  loading.value.inscribir = true;
  window.axios.post('/standard/inscripciones', {
    dni: cursante.value.dni,
    taller_id: tallerId,
  })
    .then(() => {
      $q.notify({ type: 'positive', message: 'Inscripción realizada' });
      buscarCursante();
      cargarInscripcionesHoy();
    })
    .catch(err => {
      $q.notify({ type: 'warning', message: err.response?.data?.message || 'No se pudo inscribir' });
    })
    .finally(() => {
      loading.value.inscribir = false;
    });
}

onMounted(() => {
  cargarTalleresHoy();
  cargarInscripcionesHoy();
  const params = new URLSearchParams(window.location.search);
  const dniParam = params.get('dni');
  if (dniParam) {
    dni.value = dniParam;
    buscarCursante();
    $q.notify({ type: 'info', message: 'Búsqueda precargada por DNI' });
  }
});
</script>
