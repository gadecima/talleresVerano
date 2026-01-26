<template>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-md">
                <div class="col">
                    <h4 class="q-my-none">Gestión de Talleres</h4>
                    <div class="text-subtitle2 text-grey">Listado completo de talleres registrados</div>
                </div>
            </div>

            <!-- Botón agregar taller -->
            <q-card class="q-mb-lg">
                <q-card-section class="row items-center justify-between">
                    <div class="text-h6">Talleres Registrados</div>
                    <q-btn color="primary" icon="add_circle" label="Agregar taller" @click="abrirDialogoCrear" />
                </q-card-section>
            </q-card>

            <!-- Tabla de talleres -->
            <q-card>
                <q-card-section>
                    <div class="row items-center q-mb-md">
                        <div class="col-12 col-md-6">
                            <q-input v-model="search" label="Buscar por nombre o responsable" outlined dense clearable
                                @update:model-value="buscarConDebounce">
                                <template v-slot:prepend>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-auto q-ml-auto">
                            <q-btn flat icon="refresh" label="Actualizar" @click="cargarTalleres" :loading="loading" />
                        </div>
                    </div>

                    <q-table
                        :rows="talleres" :columns="columns" row-key="id" v-model:pagination="pagination"
                        @request="onRequest" :loading="loading" flat bordered >
                        <template v-slot:body-cell-dias="props">
                            <q-td :props="props">
                                <template v-if="props.row.dias && props.row.dias.length > 0">
                                    <div>
                                        <template v-for="(dia, idx) in props.row.dias" :key="dia.id">
                                            <q-chip size="sm" color="primary" text-color="white" dense class="q-ma-xs">
                                                {{ dia.dia_semana.charAt(0).toUpperCase() + dia.dia_semana.slice(1) }}
                                            </q-chip>
                                            <template v-if="idx === 2 && props.row.dias.length > 3">
                                                <br />
                                            </template>
                                        </template>
                                    </div>
                                </template>
                                <span v-else class="text-grey">
                                    Sin días asignados
                                </span>
                            </q-td>
                        </template>
                        <template v-slot:body-cell-descripcion="props">
                            <q-td :props="props" style="max-width: 300px">
                                <div class="ellipsis" style="white-space: normal; overflow: hidden;
                                    text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ props.row.descripcion || 'Sin descripción' }}
                                </div>
                            </q-td>
                        </template>
                        <template v-slot:body-cell-disponibilidad="props">
                            <q-td :props="props" style="max-width: 300px">
                                <q-badge
                                    :color="(props.row.disponibilidad ? 'positive' : 'negative')"
                                    class="q-ml-xs"
                                    style="cursor: pointer"
                                    @click="toggleDisponibilidad(props.row)">
                                    {{ props.row.disponibilidad ? 'Disponible' : 'No disponible' }}
                                </q-badge>
                            </q-td>
                        </template>
                        <template v-slot:body-cell-actions="props">
                            <q-td :props="props">
                                <q-btn flat dense round icon="edit" color="primary" @click="editarTaller(props.row)">
                                    <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn flat dense round icon="delete" color="negative"
                                    @click="confirmarEliminar(props.row)">
                                    <q-tooltip>Eliminar</q-tooltip>
                                </q-btn>
                            </q-td>
                        </template>
                    </q-table>
                </q-card-section>
            </q-card>

            <!-- Diálogo de creación -->
            <q-dialog v-model="dialogCrear" persistent>
                <q-card style="min-width: 600px">
                    <q-card-section class="row items-center q-pb-none">
                        <div class="text-h6">Agregar Taller</div>
                        <q-space />
                        <q-btn icon="close" flat round dense v-close-popup />
                    </q-card-section>

                    <q-card-section>
                        <q-form @submit.prevent="guardarCreacion" class="q-gutter-md">
                            <div class="row q-col-gutter-md q-pl-md">
                                <div class="col-12">
                                    <q-input v-model="formCrear.nombre" label="Nombre del Taller" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-3">
                                    <q-input v-model.number="formCrear.edad_minima" label="Edad Mínima" type="number" outlined
                                    :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-3">
                                    <q-input v-model.number="formCrear.edad_maxima" label="Edad Máxima" type="number" outlined
                                    :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formCrear.espacio_fisico" label="Espacio Físico" outlined />
                                </div>

                                <div class="col-12">
                                    <q-input v-model="formCrear.descripcion" label="Descripción" type="textarea" outlined />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model.number="formCrear.cupos" label="Cupos" type="number" outlined
                                    :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-checkbox
                                        v-model="formCrear.disponibilidad" :true-value="1" :false-value="0"
                                        label="Disponible para inscripciones"
                                    />
                                </div>
                                <div class="col-12">
                                    <q-select
                                        v-model="formCrear.dias" :options="diasOptions" emit-value map-options multiple
                                        label="Días de la semana" outlined :rules="[valReqArray]" hint="Selecciona uno o más días"
                                    />
                                </div>
                                <!--
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formCrear.responsable" label="Responsable"
                                        outlined :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-select
                                        v-model="formCrear.orientado" :options="orientadoOptions"
                                        emit-value map-options label="Orientado a" outlined
                                        :rules="[valReq]"
                                    />
                                </div>
                                -->

                            </div>

                            <div class="row items-center q-mt-md">
                                <q-btn type="submit" color="primary" label="Crear taller" :loading="loadingCrear" />
                                <q-btn flat label="Cancelar" @click="dialogCrear = false" :disable="loadingCrear"
                                    class="q-ml-sm" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <!-- Diálogo de edición -->
            <q-dialog v-model="dialogEditar" persistent>
                <q-card style="min-width: 600px">
                    <q-card-section class="row items-center q-pb-none">
                        <div class="text-h6">Editar Taller</div>
                        <q-space />
                    </q-card-section>

                    <q-card-section>
                        <q-form @submit.prevent="guardarEdicion" class="q-gutter-md">
                            <div class="row q-col-gutter-md q-pl-md">
                                <div class="col-12">
                                    <q-input v-model="formEditar.nombre" label="Nombre del Taller" outlined
                                        :rules="[valReq]" />
                                </div>

                                <div class="col-12 col-md-3">
                                    <q-input v-model.number="formEditar.edad_minima" label="Edad Mínima" type="number" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-3">
                                    <q-input v-model.number="formEditar.edad_maxima" label="Edad Máxima" type="number" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.espacio_fisico" label="Espacio Físico" outlined />
                                </div>
                                <div class="col-12 col-md-6">
                                </div>
                                <div class="col-12">
                                    <q-input v-model="formEditar.descripcion" label="Descripción" type="textarea" outlined />
                                </div>
                                <div class="col-12">
                                    <q-select
                                        v-model="formEditar.dias" :options="diasOptions"
                                        emit-value map-options multiple outlined
                                        label="Días de la semana" :rules="[valReqArray]"
                                        hint="Selecciona uno o más días"
                                    />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model.number="formEditar.cupos" label="Cupos" type="number" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-checkbox
                                        v-model="formEditar.disponibilidad"
                                        :true-value="1" :false-value="0"
                                        label="Disponible para inscripciones"
                                    />
                                </div>
                                <!--
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.responsable" label="Responsable" outlined
                                        :rules="[valReq]" />
                                </div>
                                 <div class="col-12 col-md-6">
                                    <q-select
                                        v-model="formEditar.orientado" :options="orientadoOptions"
                                        emit-value map-options label="Orientado a" outlined :rules="[valReq]"
                                    />
                                </div>
                                -->
                            </div>

                            <div class="row items-center q-mt-md">
                                <q-btn type="submit" color="primary" label="Guardar cambios" :loading="loadingEditar" />
                                <q-btn flat label="Cancelar" @click="dialogEditar = false" :disable="loadingEditar"
                                    class="q-ml-sm" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <!-- Diálogo de confirmación eliminar -->
            <q-dialog v-model="dialogEliminar" persistent>
                <q-card>
                    <q-card-section class="row items-center">
                        <q-avatar icon="warning" color="warning" text-color="white" />
                        <span class="q-ml-sm">¿Está seguro que desea eliminar el taller <strong>{{
                                tallerEliminar?.nombre
                                }}</strong>?</span>
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn flat label="Cancelar" @click="dialogEliminar = false" :disable="loadingEliminar" />
                        <q-btn flat label="Eliminar" color="negative" @click="eliminarTaller"
                            :loading="loadingEliminar" />
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </q-page>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const $q = useQuasar();

const talleres = ref([]);
const loading = ref(false);
const loadingCrear = ref(false);
const loadingEditar = ref(false);
const loadingEliminar = ref(false);
const search = ref('');
let debounceTimer = null;

const pagination = ref({
    sortBy: 'created_at', descending: true,
    page: 1, rowsPerPage: 15, rowsNumber: 0,
});

    const columns = [
    { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
    { name: 'espacio_fisico', label: 'Espacio Físico', field: 'espacio_fisico', align: 'left', sortable: true },
    { name: 'edad_minima', label: 'Edad Mín', field: 'edad_minima', align: 'center' },
    { name: 'edad_maxima', label: 'Edad Máx', field: 'edad_maxima', align: 'center' },
    { name: 'dias', label: 'Días', field: 'dias', align: 'left' },
    { name: 'descripcion', label: 'Descripción', field: 'descripcion', align: 'left', style: 'max-width: 300px' },
    { name: 'cupos', label: 'Cupos', field: 'cupos', align: 'center' },
    { name: 'disponibilidad', label: 'Disponibilidad', field: 'disponibilidad', align: 'center', sortable: false },
    { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
    // { name: 'orientado', label: 'Orientado a', field: 'orientado', align: 'left', sortable: true, format: (val) => val ? (val.charAt(0).toUpperCase() + val.slice(1)) : '' },
    // { name: 'responsable', label: 'Responsable', field: 'responsable', align: 'left', sortable: true },
];

const orientadoOptions = [
    { label: 'Inicial', value: 'inicial' },
    { label: 'Primario', value: 'primario' },
    { label: 'Secundario', value: 'secundario' },
    { label: 'Indefinido', value: 'indefinido' },
];

const diasOptions = [
    { label: 'Lunes', value: 'lunes' },
    { label: 'Martes', value: 'martes' },
    { label: 'Miércoles', value: 'miercoles' },
    { label: 'Jueves', value: 'jueves' },
    { label: 'Viernes', value: 'viernes' },
    { label: 'Sábado', value: 'sabado' },
    { label: 'Domingo', value: 'domingo' },
];

const dialogCrear = ref(false);
const dialogEditar = ref(false);
const dialogEliminar = ref(false);
const tallerEliminar = ref(null);

const formCrear = reactive({
    nombre: '',
    responsable: '',
    edad_minima: null,
    edad_maxima: null,
    espacio_fisico: '',
    descripcion: '',
    cupos: null,
    orientado: null,
    dias: [],
    disponibilidad: 1,
});

const formEditar = reactive({
    id: null,
    nombre: '',
    responsable: '',
    edad_minima: null,
    edad_maxima: null,
    espacio_fisico: '',
    descripcion: '',
    cupos: null,
    orientado: null,
    dias: [],
    disponibilidad: 1,
});

function valReq(v) {
    return (!!v || v === 0) || 'Campo obligatorio';
}

function valReqArray(v) {
    return (v && v.length > 0) || 'Selecciona al menos un día';
}

function buscarConDebounce() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        pagination.value.page = 1;
        cargarTalleres();
    }, 500);
}

function onRequest(props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    pagination.value.page = page;
    pagination.value.rowsPerPage = rowsPerPage;
    pagination.value.sortBy = sortBy;
    pagination.value.descending = descending;
    cargarTalleres();
}

function cargarTalleres() {
    loading.value = true;
    const params = {
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage,
    };
    if (search.value) {
        params.search = search.value;
    }
    if (pagination.value.sortBy) {
        params.sortBy = pagination.value.sortBy;
        params.sortDesc = pagination.value.descending;
    }

    window.axios.get('/admin/talleres', { params })
        .then(res => {
            talleres.value = res.data.data;
            pagination.value.rowsNumber = res.data.total;
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: 'Error al cargar talleres' });
        })
        .finally(() => {
            loading.value = false;
        });
}

function abrirDialogoCrear() {
    formCrear.nombre = '';
    formCrear.responsable = '';
    formCrear.edad_minima = null;
    formCrear.edad_maxima = null;
    formCrear.espacio_fisico = '';
    formCrear.descripcion = '';
    formCrear.dias = [];
    formCrear.cupos = null;
    formCrear.disponibilidad = 1;
    dialogCrear.value = true;
}

function guardarCreacion() {
    loadingCrear.value = true;
    const dataToSend = { ...formCrear };
    window.axios.post('/admin/talleres', dataToSend)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Taller creado correctamente' });
            dialogCrear.value = false;
            cargarTalleres();
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                const first = Object.values(errors).flat()[0];
                $q.notify({ type: 'warning', message: first || 'Revisa los datos del formulario' });
            } else {
                $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo crear' });
            }
        })
        .finally(() => {
            loadingCrear.value = false;
        });
}

function editarTaller(taller) {
    formEditar.id = taller.id;
    formEditar.nombre = taller.nombre;
    formEditar.responsable = taller.responsable;
    formEditar.edad_minima = taller.edad_minima;
    formEditar.edad_maxima = taller.edad_maxima;
    formEditar.espacio_fisico = taller.espacio_fisico || '';
    formEditar.descripcion = taller.descripcion || '';
    formEditar.cupos = taller.cupos;
    formEditar.orientado = taller.orientado;
    formEditar.dias = taller.dias ? taller.dias.map(d => d.dia_semana) : [];
    formEditar.disponibilidad = typeof taller.disponibilidad === 'number' ? taller.disponibilidad : (taller.disponibilidad ? 1 : 0);
    dialogEditar.value = true;
}

function guardarEdicion() {
    loadingEditar.value = true;
    const dataToSend = { ...formEditar };
    window.axios.put(`/admin/talleres/${formEditar.id}`, dataToSend)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Taller actualizado correctamente' });
            dialogEditar.value = false;
            cargarTalleres();
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                const first = Object.values(errors).flat()[0];
                $q.notify({ type: 'warning', message: first || 'Revisa los datos del formulario' });
            } else {
                $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo actualizar' });
            }
        })
        .finally(() => {
            loadingEditar.value = false;
        });
}

function confirmarEliminar(taller) {
    tallerEliminar.value = taller;
    dialogEliminar.value = true;
}

function eliminarTaller() {
    loadingEliminar.value = true;
    window.axios.delete(`/admin/talleres/${tallerEliminar.value.id}`)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Taller eliminado correctamente' });
            dialogEliminar.value = false;
            cargarTalleres();
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo eliminar' });
        })
        .finally(() => {
            loadingEliminar.value = false;
        });
}

function toggleDisponibilidad(taller) {
    const nuevoEstado = taller.disponibilidad ? 0 : 1;
    const mensaje = nuevoEstado ? 'disponible' : 'no disponible';

    window.axios.put(`/admin/talleres/${taller.id}`, {
        ...taller,
        disponibilidad: nuevoEstado,
        dias: taller.dias ? taller.dias.map(d => d.dia_semana) : []
    })
        .then(() => {
            $q.notify({ type: 'positive', message: `Taller marcado como ${mensaje}` });
            cargarTalleres();
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: err.response?.data?.message || 'Error al actualizar disponibilidad' });
        });
}

onMounted(() => {
    cargarTalleres();
});
</script>
