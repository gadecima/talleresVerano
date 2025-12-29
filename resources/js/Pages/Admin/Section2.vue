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
                        :rows="talleres"
                        :columns="columns"
                        row-key="id"
                        v-model:pagination="pagination"
                        @request="onRequest"
                        :loading="loading"
                        flat
                        bordered
                    >
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
                            <div class="row q-col-gutter-md">
                                <div class="col-12">
                                    <q-input v-model="formCrear.nombre" label="Nombre del Taller" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formCrear.responsable" label="Responsable" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formCrear.orientado" label="Orientado a" outlined
                                        :rules="[valReq]" />
                                </div>
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
                        <q-btn icon="close" flat round dense v-close-popup />
                    </q-card-section>

                    <q-card-section>
                        <q-form @submit.prevent="guardarEdicion" class="q-gutter-md">
                            <div class="row q-col-gutter-md">
                                <div class="col-12">
                                    <q-input v-model="formEditar.nombre" label="Nombre del Taller" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.responsable" label="Responsable" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.orientado" label="Orientado a" outlined
                                        :rules="[valReq]" />
                                </div>
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
    sortBy: 'created_at',
    descending: true,
    page: 1,
    rowsPerPage: 15,
    rowsNumber: 0,
});

const columns = [
    { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
    { name: 'responsable', label: 'Responsable', field: 'responsable', align: 'left', sortable: true },
    { name: 'orientado', label: 'Orientado a', field: 'orientado', align: 'left', sortable: true },
    { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
];

const dialogCrear = ref(false);
const dialogEditar = ref(false);
const dialogEliminar = ref(false);
const tallerEliminar = ref(null);

const formCrear = reactive({
    nombre: '',
    responsable: '',
    orientado: '',
});

const formEditar = reactive({
    id: null,
    nombre: '',
    responsable: '',
    orientado: '',
});

function valReq(v) {
    return (!!v || v === 0) || 'Campo obligatorio';
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
    formCrear.orientado = '';
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
    formEditar.orientado = taller.orientado;
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

onMounted(() => {
    cargarTalleres();
});
</script>
