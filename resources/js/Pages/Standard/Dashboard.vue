<template>
    <StandardLayout>
        <q-page class="q-pa-sm">
            <div class="row items-center">
                <div class="col">
                    <h4 class="q-my-none">Bienvenido, {{ user.name }}</h4>
                    <p class="text-subtitle2 text-grey">Dashboard de Usuario Estándar</p>
                </div>
            </div>

            <!-- Contadores del día -->
            <div class="row q-col-gutter-md q-mt-sm">
                <div class="col-12 col-md-4">
                    <q-card class="contador-card clickable" @click="mostrarModalInscripciones">
                        <q-card-section class="q-pa-sm q-pb-xs">
                            <div class="text-subtitle2 q-mb-xs">Cursantes Inscriptos Hoy</div>
                            <div class="text-h4 text-primary">{{ contadores.inscriptosHoy }}</div>
                            <p class="text-caption text-grey q-my-xs">click aquí para ver inscripciones y exportar</p>
                        </q-card-section>
                        <q-linear-progress :value="contadores.totalCursantes > 0 ? contadores.inscriptosHoy / contadores.totalCursantes : 0"
                            color="primary" size="3px" />
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="contador-card">
                        <q-card-section class="q-pa-sm q-pb-xs">
                            <div class="text-subtitle2 q-mb-xs">Nuevos Cursantes Hoy</div>
                            <div class="text-h4 text-secondary">{{ contadores.nuevosHoy }}/{{ contadores.totalCursantes }}</div>
                            <p class="text-caption text-grey q-my-xs">Nuevos de {{ contadores.totalCursantes }} totales</p>
                        </q-card-section>
                        <q-linear-progress :value="contadores.totalCursantes > 0 ? contadores.nuevosHoy / contadores.totalCursantes : 0"
                            color="secondary" size="3px" />
                    </q-card>
                </div>

                <div class="col-12 col-md-4">
                    <q-card class="contador-card clickable" @click="mostrarModalTalleres">
                        <q-card-section class="q-pa-sm q-pb-xs">
                            <div class="text-subtitle2 q-mb-xs">Talleres Disponibles Hoy</div>
                            <div class="text-h4 text-accent">{{ todosLosTalleres.length }}</div>
                            <p class="text-caption text-grey q-my-xs">Click para ver detalles</p>
                        </q-card-section>
                        <q-linear-progress :value="1" color="accent" size="3px" />
                    </q-card>
                </div>
            </div>

            <!-- Acceso rápido: Registrar nuevo cursante -->
            <q-card class="q-mt-md">
                <q-card-section class="row items-center justify-between">
                    <div class="text-h6">Registro de Cursante</div>
                        <q-btn color="primary" icon="person_add" label="Registrar nuevo cursante" href="/standard/section1" />
                        <q-btn color="secondary" icon="groups" label="Cursantes registrados" href="/standard/section2" />
                </q-card-section>
            </q-card>

            <!-- Búsqueda de cursante por DNI y asignación de talleres -->
            <q-card class="q-mt-md">
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
                                            <q-item-label>Edad</q-item-label>
                                            <q-item-label caption>{{ cursante.edad }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>

                        <div v-if="cursante" class="col-12 col-md-7">
                            <div class="row items-center">
                                <div class="col-auto">
                                    <div class="text-h6">Talleres disponibles hoy ({{ diaHoy }})</div>
                                </div>
                                <div class="col">
                                    <q-btn flat icon="refresh" label="Actualizar" @click="cargarTalleresHoy"
                                        :loading="loading.talleres" class="q-ml-sm"/>
                                </div>
                            </div>

                            <q-banner v-if="talleresHoy.length === 0" class="q-my-md" dense>
                                {{ cursante ? 'No hay talleres disponibles para este cursante hoy (edad o limite alcanzado).'
                                    : 'No hay talleres disponibles para hoy.' }}
                            </q-banner>

                            <q-list v-else bordered class="q-mt-md">
                                <q-item v-for="t in talleresHoy" :key="t.id">
                                    <q-item-section>
                                        <q-item-label>{{ t.nombre }}</q-item-label>
                                        <q-item-label caption>
                                            Edad: {{ t.edad_minima }} - {{ t.edad_maxima }} · Descripción: {{ t.descripcion }}
                                            <br/>
                                            Cupos: {{ t.inscriptos_en_fecha ?? 0 }} / {{ t.cupos ?? 'N/A' }}
                                        </q-item-label>
                                    </q-item-section>
                                    <q-item-section side>
                                        <q-btn color="secondary" label="Inscribir"
                                            :disable="!cursante || t.completo === true"
                                            @click="inscribir(t.id)"/>
                                    </q-item-section>
                                </q-item>
                            </q-list>

                            <div v-if="cursante" class="q-mt-lg">
                                <div class="text-subtitle1">Inscripciones hoy del cursante ({{ inscripcionesCursante.length }}/2)</div>
                                <q-banner v-if="inscripcionesCursante.length === 0" class="q-my-md" dense>
                                    El cursante no tiene inscripciones para hoy
                                </q-banner>
                                <q-list v-else bordered>
                                    <q-item v-for="ins in inscripcionesCursante" :key="ins.id">
                                        <q-item-section>
                                            <q-item-label>{{ ins.taller.nombre }}</q-item-label>
                                            <q-item-label caption>{{ formatDate(ins.fecha) }}</q-item-label>
                                        </q-item-section>
                                        <q-item-section side>
                                            <q-btn
                                                flat dense size="sm" icon="close" color="negative"
                                                label="Desinscrbir"
                                                title="Desinscribir del taller"
                                                @click="desinscribirCursanteDeTaller(ins)"
                                            />
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>

            <!-- Inscripciones del día (todos los cursantes) -->
            <q-card class="q-mt-md">
                <q-card-section>
                    <div class="row items-center">
                        <div class="col">
                            <div class="text-h6">Inscripciones de hoy ({{ formatDate(hoyFecha) }})</div>
                        </div>
                        <div class="col-auto">
                            <q-btn flat icon="refresh" label="Actualizar" @click="cargarInscripcionesHoy"
                                :loading="loading.inscripciones"/>
                        </div>
                    </div>
                    <q-table :rows="inscripcionesHoy" :columns="columns"
                        row-key="id" dense flat class="q-mt-md">
                        <template v-slot:body-cell-acciones="props">
                            <q-td :props="props">
                                <q-btn
                                    flat dense size="sm" icon="close" color="negative"
                                    title="Eliminar inscripción" @click="desinscribir(props.row)"
                                />
                            </q-td>
                        </template>
                    </q-table>
                </q-card-section>
            </q-card>

            <!-- Modal de Talleres del Día -->
            <q-dialog v-model="modalTalleres">
                <q-card style="min-width: 700px; max-width: 90vw;">
                    <q-card-section>
                        <div class="row items-center">
                            <div class="col">
                                <div class="text-h6">Talleres Disponibles Hoy ({{ diaHoy }})</div>
                            </div>
                            <div class="col-auto">
                                <q-btn flat icon="refresh" label="Actualizar" @click="cargarTodosLosTalleres"
                                    :loading="loading.todosLosTalleres" />
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section class="q-pt-none">
                        <q-table
                            :rows="todosLosTalleres" :columns="columnsTalleres"
                            row-key="id" dense flat class="q-mt-md" :rows-per-page-options="[10, 20, 50]" >
                            <template v-slot:body-cell-dias="props">
                                <q-td :props="props">
                                    <q-badge v-for="dia in props.row.dias" :key="dia.id" color="primary" class="q-mr-xs">
                                        {{ dia.dia_semana }}
                                    </q-badge>
                                </q-td>
                            </template>
                            <template v-slot:body-cell-cupos="props">
                                <q-td :props="props" >
                                    <q-badge :color="(props.row.completo ? 'negative' : 'positive')" class="q-ml-xs">
                                        {{ (props.row.inscriptos_en_fecha ?? 0) }} / {{ (props.row.cupos ?? 'N/A') }}
                                    </q-badge>
                                </q-td>
                            </template>
                        </q-table>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions align="right">
                        <q-btn flat label="Cerrar" color="primary" @click="modalTalleres = false" />
                    </q-card-actions>
                </q-card>
            </q-dialog>

            <!-- Modal de Inscripciones por Taller -->
            <q-dialog v-model="modalInscripciones">
                <q-card style="min-width: 800px; max-width: 90vw;">
                    <q-card-section>
                        <div class="row items-center">
                            <div class="col">
                                <div class="text-h6">Inscripciones por Taller - Hoy ({{ diaHoy }})</div>
                            </div>
                            <div class="col-auto">
                                <q-btn flat icon="picture_as_pdf" label="Exportar listado" class="q-mr-sm"
                                    @click="exportarListadoPorTaller" :loading="loading.exportarListado" />
                                <q-btn flat icon="refresh" label="Actualizar" @click="cargarDetallesInscripciones"
                                    :loading="loading.detallesInscripciones" />
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-section class="q-pt-none">
                        <q-table
                            :rows="detallesInscripciones" :columns="columnsDetallesInscripciones"
                            row-key="taller_id" dense flat class="q-mt-md" >
                            <template v-slot:body-cell-acciones="props">
                                <q-td :props="props">
                                    <q-btn
                                        flat dense size="sm" icon="description" color="primary"
                                        title="Ver inscriptos" class="q-mr-xs"
                                        @click="verInscriptosTaller(props.row)"
                                    />
                                </q-td>
                            </template>
                        </q-table>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions align="right">
                        <q-btn flat label="Cerrar" color="primary" @click="modalInscripciones = false" />
                    </q-card-actions>
                </q-card>
            </q-dialog>

            <!-- Modal de Inscriptos de un Taller Específico -->
            <q-dialog v-model="modalInscriptosTaller" maximized>
                <q-card>
                    <q-card-section class="row items-center bg-primary text-white">
                        <div class="col">
                            <div class="text-h6">{{ tallerSeleccionado?.nombre_taller }}</div>
                            <div class="text-caption">Inscriptos el día {{ formatDate(hoyFecha) }}</div>
                        </div>
                        <q-space />
                        <q-btn
                            flat icon="picture_as_pdf" label="Exportar PDF" color="white" class="q-mr-sm"
                            @click="exportarPdf(tallerSeleccionado)"
                        />
                        <q-btn
                            flat icon="table_chart" label="Exportar Excel" color="white" class="q-mr-sm"
                            @click="exportarExcel(tallerSeleccionado)"
                        />
                        <q-btn flat icon="close" color="white" @click="modalInscriptosTaller = false" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <div v-if="inscriptosTaller.length === 0" class="text-center text-grey q-pa-lg">
                            <q-icon name="info" size="48px" color="grey" />
                            <p class="q-mt-md">No hay inscriptos en este taller para el día de hoy</p>
                        </div>
                        <q-table
                            v-else :rows="inscriptosTaller" :columns="columnsInscriptosTaller" row-key="id" dense flat
                            :rows-per-page-options="[10, 20, 50, 0]" :pagination="{ rowsPerPage: 20 }" >
                            <template v-slot:body-cell-index="props">
                                <q-td :props="props">
                                    {{ props.rowIndex + 1 }}
                                </q-td>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </q-dialog>
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
    // Si viene "YYYY-MM-DD" (backend), parsear como hora local para evitar desfase UTC
    if (typeof date === 'string' && date.match(/^\d{4}-\d{2}-\d{2}$/)) {
        const [year, month, day] = date.split('-');
        return new Date(year, month - 1, day).toLocaleDateString('es-ES');
    }
    return new Date(date).toLocaleDateString('es-ES');
};

const getTodayString = () => {
    const today = new Date();
    return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
};

const $q = useQuasar();

const hoyFecha = ref(getTodayString());
const diaHoy = ref(new Date().toLocaleDateString('es-ES', { weekday: 'long' }));

// Estado y datos
const dni = ref('');
const cursante = ref(null);
const talleresHoy = ref([]); // Talleres filtrados por cursante
const todosLosTalleres = ref([]); // Todos los talleres del día
const inscripcionesCursante = ref([]);
const inscripcionesHoy = ref([]);
const contadores = ref({ inscriptosHoy: 0, nuevosHoy: 0, totalCursantes: 0 });
const modalTalleres = ref(false);
const modalInscripciones = ref(false);
const modalInscriptosTaller = ref(false);
const detallesInscripciones = ref([]);
const tallerSeleccionado = ref(null);
const inscriptosTaller = ref([]);

const loading = ref({
    buscar: false, talleres: false, inscripciones: false, inscribir: false, contadores: false,
    todosLosTalleres: false, detallesInscripciones: false, exportarListado: false
});

const columns = [
    { name: 'cursante', label: 'Cursante', field: row => row.cursante.nombre_apellido, align: 'left' },
    { name: 'dni', label: 'DNI', field: row => row.cursante.dni, align: 'left' },
    { name: 'taller', label: 'Taller', field: row => row.taller.nombre, align: 'left' },
    { name: 'edad', label: 'Edad', field: row => row.cursante.edad, align: 'left' },
    { name: 'hora', label: 'Hora', field: row => new Date(row.created_at).toLocaleTimeString('es-ES'), align: 'left' },
    { name: 'acciones', label: 'Desinscribir', field: 'acciones', align: 'center' },
];

const columnsTalleres = [
    { name: 'nombre', label: 'Taller', field: 'nombre', align: 'left', sortable: true },
    { name: 'edad_minima', label: 'Edad Mín', field: 'edad_minima', align: 'center', sortable: true },
    { name: 'edad_maxima', label: 'Edad Máx', field: 'edad_maxima', align: 'center', sortable: true },
    { name: 'dias', label: 'Días', field: 'dias', align: 'left' },
    { name: 'cupos', label: 'Inscriptops/cupo', field: 'cupos', align: 'center' },
    { name: 'descripcion', label: 'Descripción', field: 'descripcion', align: 'left', sortable: false },
];

const columnsDetallesInscripciones = [
    { name: 'nombre_taller', label: 'Taller', field: 'nombre_taller', align: 'left', sortable: true },
    { name: 'espacio', label: 'Espacio', field: 'espacio', align: 'center', sortable: true },
    { name: 'cantidad_inscriptos', label: 'Inscritos', field: 'cantidad_inscriptos', align: 'center', sortable: true },
    { name: 'acciones', label: 'Acciones', field: 'acciones', align: 'center' },
];

const columnsInscriptosTaller = [
    { name: 'index', label: '#', field: 'index', align: 'center' },
    { name: 'nombre', label: 'Nombre y Apellido', field: row => row.cursante.nombre_apellido, align: 'left', sortable: true },
    { name: 'dni', label: 'DNI', field: row => row.cursante.dni, align: 'left', sortable: true },
    { name: 'edad', label: 'Edad', field: row => row.cursante.edad, align: 'center', sortable: true },
    { name: 'contacto', label: 'Contacto', field: row => row.cursante.contacto || 'N/A', align: 'left', sortable: false },
    { name: 'hora', label: 'Hora Inscripción', field: row => new Date(row.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }), align: 'center', sortable: true },
];

const actualizarDisponibilidad = (data) => {
    if (Array.isArray(data.talleres_disponibles)) {
        talleresHoy.value = data.talleres_disponibles;
    }
    if (data.fecha_iso) {
        hoyFecha.value = new Date(data.fecha_iso);
    } else if (data.fecha) {
        hoyFecha.value = data.fecha;
    }
    if (data.dia) {
        diaHoy.value = data.dia;
    }
};

function buscarCursante() {
    if (!dni.value) return;
    loading.value.buscar = true;
    window.axios.get(`/standard/cursantes/buscar/${encodeURIComponent(dni.value)}`)
        .then(res => {
            cursante.value = res.data.cursante;
            inscripcionesCursante.value = res.data.inscripciones || [];
            actualizarDisponibilidad(res.data);
        })
        .catch(err => {
            cursante.value = null;
            inscripcionesCursante.value = [];
            talleresHoy.value = [];
            $q.notify({ type: 'negative', message: err.response?.data?.message || 'Error al buscar cursante' });
        })
        .finally(() => {
            loading.value.buscar = false;
        });
}

function cargarTalleresHoy() {
    if (!cursante.value) return;
    loading.value.talleres = true;
    window.axios.get(`/standard/cursantes/buscar/${encodeURIComponent(cursante.value.dni)}`)
        .then(res => {
            if (res.data.cursante) {
                cursante.value = res.data.cursante;
            }
            inscripcionesCursante.value = res.data.inscripciones || inscripcionesCursante.value;
            actualizarDisponibilidad(res.data);
        })
        .catch(() => {
            $q.notify({ type: 'warning', message: 'No se pudieron cargar los talleres disponibles' });
        })
        .finally(() => {
            loading.value.talleres = false;
        });
}

function cargarTodosLosTalleres() {
    loading.value.todosLosTalleres = true;
    window.axios.get('/standard/talleres/hoy')
        .then(res => {
            todosLosTalleres.value = res.data.talleres || [];
            // Preferir fecha_iso (incluye offset) para evitar desfase horario
            hoyFecha.value = res.data.fecha_iso ? new Date(res.data.fecha_iso) : (res.data.fecha || hoyFecha.value);
            diaHoy.value = res.data.dia || diaHoy.value;
        })
        .catch(() => {
            $q.notify({ type: 'warning', message: 'No se pudieron cargar los talleres' });
        })
        .finally(() => {
            loading.value.todosLosTalleres = false;
        });
}

function mostrarModalTalleres() {
    modalTalleres.value = true;
    if (todosLosTalleres.value.length === 0) {
        cargarTodosLosTalleres();
    }
}

function mostrarModalInscripciones() {
    modalInscripciones.value = true;
    cargarDetallesInscripciones();
}

function cargarDetallesInscripciones() {
    loading.value.detallesInscripciones = true;
    window.axios.get('/standard/inscripciones/detalles-hoy')
        .then(res => {
            console.log('Respuesta detalles:', res.data);
            detallesInscripciones.value = res.data.detalles || [];
        })
        .catch(err => {
            console.error('Error cargando detalles:', err.response?.data || err.message);
            $q.notify({
                type: 'warning', message: err.response?.data?.message ||
                    'No se pudieron cargar los detalles de inscripciones'
            });
        })
        .finally(() => {
            loading.value.detallesInscripciones = false;
        });
}

function verInscriptosTaller(taller) {
    tallerSeleccionado.value = taller;

    // Cargar los inscriptos del taller
    window.axios.get(`/standard/inscripciones/hoy?taller_id=${taller.taller_id}`)
        .then(res => {
            inscriptosTaller.value = res.data.inscripciones || [];
            modalInscriptosTaller.value = true;
        })
        .catch(err => {
            $q.notify({ type: 'warning', message: 'Error al cargar inscriptos del taller' });
        });
}

function exportarListadoPorTaller() {
    loading.value.exportarListado = true;

    const fechaExport = typeof hoyFecha.value === 'string'
        ? hoyFecha.value
        : hoyFecha.value.toISOString().split('T')[0];

    const url = `/standard/inscripciones/export/pdf?fecha=${fechaExport}`;
    window.open(url, '_blank');

    setTimeout(() => {
        loading.value.exportarListado = false;
    }, 500);
}

function exportarPdf(taller) {
    if (!taller || !taller.taller_id) {
        $q.notify({ type: 'warning', message: 'No se pudo identificar el taller' });
        return;
    }

    const fechaExport = typeof hoyFecha.value === 'string'
        ? hoyFecha.value
        : hoyFecha.value.toISOString().split('T')[0];

    const url = `/standard/talleres/${taller.taller_id}/dia/${fechaExport}/export/pdf`;
    window.open(url, '_blank');
}

function exportarExcel(taller) {
    if (!taller || !taller.taller_id) {
        $q.notify({ type: 'warning', message: 'No se pudo identificar el taller' });
        return;
    }

    const fechaExport = typeof hoyFecha.value === 'string'
        ? hoyFecha.value
        : hoyFecha.value.toISOString().split('T')[0];

    const url = `/standard/talleres/${taller.taller_id}/dia/${fechaExport}/export/excel`;
    window.open(url, '_blank');
}

function cargarInscripcionesHoy() {
    loading.value.inscripciones = true;
    window.axios.get('/standard/inscripciones/hoy')
        .then(res => {
            inscripcionesHoy.value = res.data.inscripciones || [];
            hoyFecha.value = res.data.fecha_iso ? new Date(res.data.fecha_iso) : (res.data.fecha || hoyFecha.value);
        })
        .finally(() => {
            loading.value.inscripciones = false;
        });
}

function cargarContadores() {
    loading.value.contadores = true;
    window.axios.get('/standard/contadores')
        .then(res => {
            contadores.value = res.data;
        })
        .catch(err => {
            $q.notify({ type: 'warning', message: 'Error al cargar los contadores' });
        })
        .finally(() => {
            loading.value.contadores = false;
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
            cargarContadores();
        })
        .catch(err => {
            $q.notify({ type: 'warning', message: err.response?.data?.message || 'No se pudo inscribir' });
        })
        .finally(() => {
            loading.value.inscribir = false;
        });
}

function desinscribir(inscripcion) {
    $q.dialog({
        title: 'Confirmar',
        message: `¿Está seguro de eliminar la inscripción de ${inscripcion.cursante.nombre_apellido} en ${inscripcion.taller.nombre}?`,
        cancel: true,
        persistent: true
    }).onOk(() => {
        window.axios.delete(`/standard/inscripciones/${inscripcion.id}`)
            .then(() => {
                $q.notify({ type: 'positive', message: 'Inscripción eliminada exitosamente' });
                cargarInscripcionesHoy();
                cargarContadores();
                cargarDetallesInscripciones();
                // Si hay un cursante cargado, actualizar su información
                if (cursante.value) {
                    buscarCursante();
                }
            })
            .catch(err => {
                $q.notify({ type: 'negative', message: err.response?.data?.message || 'Error al eliminar inscripción' });
            });
    });
}

function desinscribirCursanteDeTaller(inscripcion) {
    $q.dialog({
        title: 'Desinscribir',
        message: `¿Desea desinscribir a ${cursante.value.nombre_apellido} del taller "${inscripcion.taller.nombre}"?`,
        cancel: true,
        persistent: true
    }).onOk(() => {
        window.axios.delete(`/standard/inscripciones/${inscripcion.id}`)
            .then(() => {
                $q.notify({
                    type: 'positive',
                    message: `Desinscripción realizada. El cursante puede inscribirse en otros talleres.`
                });
                // Recargar todos los datos necesarios
                buscarCursante(); // Actualiza inscripciones del cursante
                cargarInscripcionesHoy(); // Actualiza tabla general de inscripciones
                cargarContadores(); // Actualiza contadores
                cargarDetallesInscripciones(); // Actualiza detalles por taller
            })
            .catch(err => {
                $q.notify({
                    type: 'negative',
                    message: err.response?.data?.message || 'Error al desinscribir del taller'
                });
            });
    });
}

onMounted(() => {
    cargarTodosLosTalleres();
    cargarInscripcionesHoy();
    cargarContadores();
    const params = new URLSearchParams(window.location.search);
    const dniParam = params.get('dni');
    if (dniParam) {
        dni.value = dniParam;
        buscarCursante();
        $q.notify({ type: 'info', message: 'Búsqueda precargada por DNI' });
    }
});
</script>

<style scoped>
.contador-card {
    height: auto;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.contador-card :deep(.q-card__section) {
    flex-grow: 1;
}

.clickable {
    cursor: pointer;
    transition: transform 0.2s;
}

.clickable:hover {
    transform: scale(1.02);
}
</style>
