<template>
    <StandardLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-md">
                <div class="col">
                    <h4 class="q-my-none">Gestión de Cursantes</h4>
                    <div class="text-subtitle2 text-grey">Listado completo de cursantes registrados</div>
                </div>
            </div>

            <!-- Botón agregar cursante -->
            <q-card class="q-mb-lg">
                <q-card-section class="row items-center justify-between">
                    <div class="text-h6">Cursantes Registrados</div>
                    <q-btn color="primary" icon="person_add" label="Agregar cursante" href="/standard/section1" />
                </q-card-section>
            </q-card>

            <!-- Tabla de cursantes -->
            <q-card>
                <q-card-section>
                    <div class="row items-center q-mb-md">
                        <div class="col-12 col-md-6">
                            <q-input v-model="search" label="Buscar por nombre o DNI" outlined dense clearable
                                @update:model-value="buscarConDebounce">
                                <template v-slot:prepend>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-auto q-ml-auto">
                            <q-btn flat icon="refresh" label="Actualizar" @click="cargarCursantes" :loading="loading" />
                        </div>
                    </div>

                    <q-table
                        :rows="cursantes"
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
                                <q-btn flat dense round icon="edit" color="primary" @click="editarCursante(props.row)">
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

            <!-- Diálogo de edición -->
            <q-dialog v-model="dialogEditar" persistent>
                <q-card style="min-width: 600px">
                    <q-card-section class="row items-center q-pb-none">
                        <div class="text-h6">Editar Cursante</div>
                        <q-space />
                        <q-btn icon="close" flat round dense v-close-popup />
                    </q-card-section>

                    <q-card-section>
                        <q-form @submit.prevent="guardarEdicion" class="q-gutter-md">
                            <div class="row q-col-gutter-md">
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.nombre_apellido" label="Nombre y Apellido" outlined
                                        :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.dni" label="DNI" outlined :rules="[valReq, valDni]"
                                        mask="##########" maxlength="10" />
                                </div>

                                <div class="col-12 col-md-6">
                                    <q-input v-model="formEditar.fecha_nacimiento" type="date"
                                        label="Fecha de nacimiento" outlined :rules="[valReq]" />
                                </div>
                                <div class="col-12 col-md-6">
                                    <q-select v-model="formEditar.localidad" :options="localidades" label="Localidad"
                                        outlined :rules="[valReq]" emit-value map-options />
                                </div>

                                <div class="col-12 col-md-4">
                                    <q-input v-model="formEditar.contacto" label="Contacto" outlined />
                                </div>
                                <div class="col-12 col-md-4">
                                    <q-input v-model="formEditar.correo" type="email" label="Correo" outlined
                                        :rules="[valEmail]" />
                                </div>
                                <div class="col-12 col-md-4">
                                    <q-select v-model="formEditar.nivel_educativo" :options="niveles"
                                        label="Nivel educativo" outlined :rules="[valReq]" emit-value map-options />
                                </div>

                                <div class="col-12">
                                    <q-input v-model="formEditar.escuela" label="Escuela" outlined />
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
                        <span class="q-ml-sm">¿Está seguro que desea eliminar a <strong>{{
                                cursanteEliminar?.nombre_apellido
                                }}</strong>?</span>
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn flat label="Cancelar" @click="dialogEliminar = false" :disable="loadingEliminar" />
                        <q-btn flat label="Eliminar" color="negative" @click="eliminarCursante"
                            :loading="loadingEliminar" />
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </q-page>
    </StandardLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import StandardLayout from '../../Layouts/StandardLayout.vue';

const $q = useQuasar();

const cursantes = ref([]);
const loading = ref(false);
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
    { name: 'nombre_apellido', label: 'Nombre y Apellido', field: 'nombre_apellido', align: 'left', sortable: true },
    { name: 'dni', label: 'DNI', field: 'dni', align: 'left', sortable: true },
    { name: 'fecha_nacimiento', label: 'Fecha Nac.', field: 'fecha_nacimiento', align: 'left', format: (val) => new Date(val).toLocaleDateString('es-ES') },
    { name: 'localidad', label: 'Localidad', field: 'localidad', align: 'left' },
    { name: 'nivel_educativo', label: 'Nivel', field: 'nivel_educativo', align: 'left' },
    { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
];

const niveles = [
    { label: 'Inicial', value: 'inicial' },
    { label: 'Primario', value: 'primario' },
    { label: 'Secundario', value: 'secundario' },
];

const localidades = [
    { label: 'San Miguel de Tucumán', value: 'San Miguel de Tucumán' },
    { label: 'Yerba Buena', value: 'Yerba Buena' },
    { label: 'Tafí Viejo', value: 'Tafí Viejo' },
    { label: 'Las Talitas', value: 'Las Talitas' },
    { label: 'Alderetes', value: 'Alderetes' },
    { label: 'Banda del Río Salí', value: 'Banda del Río Salí' },
    { label: 'Lules', value: 'Lules' },
    { label: 'Famaillá', value: 'Famaillá' },
    { label: 'Monteros', value: 'Monteros' },
    { label: 'Concepción', value: 'Concepción' },
    { label: 'Aguilares', value: 'Aguilares' },
    { label: 'Bella Vista', value: 'Bella Vista' },
    { label: 'Simoca', value: 'Simoca' },
    { label: 'Graneros', value: 'Graneros' },
    { label: 'Burruyacu', value: 'Burruyacu' },
    { label: 'Trancas', value: 'Trancas' },
    { label: 'La Cocha', value: 'La Cocha' },
    { label: 'Tafí del Valle', value: 'Tafí del Valle' },
    { label: 'Leales', value: 'Leales' },
];

const dialogEditar = ref(false);
const dialogEliminar = ref(false);
const cursanteEliminar = ref(null);

const formEditar = reactive({
    id: null,
    nombre_apellido: '',
    dni: '',
    fecha_nacimiento: '',
    localidad: null,
    contacto: '',
    correo: '',
    escuela: '',
    nivel_educativo: null,
});

function valReq(v) {
    return (!!v || v === 0) || 'Campo obligatorio';
}

function valDni(v) {
    if (!v) return true;
    if (!/^[0-9]+$/.test(v)) return 'El DNI debe contener solo números';
    return v.length === 8 || 'El DNI debe tener exactamente 8 dígitos';
}

function valEmail(v) {
    if (!v) return true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(v) || 'El correo electrónico no es válido';
}

function buscarConDebounce() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        pagination.value.page = 1;
        cargarCursantes();
    }, 500);
}

function onRequest(props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    pagination.value.page = page;
    pagination.value.rowsPerPage = rowsPerPage;
    pagination.value.sortBy = sortBy;
    pagination.value.descending = descending;
    cargarCursantes();
}

function cargarCursantes() {
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

    window.axios.get('/standard/cursantes', { params })
        .then(res => {
            cursantes.value = res.data.data;
            pagination.value.rowsNumber = res.data.total;
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: 'Error al cargar cursantes' });
        })
        .finally(() => {
            loading.value = false;
        });
}

function editarCursante(cursante) {
    formEditar.id = cursante.id;
    formEditar.nombre_apellido = cursante.nombre_apellido;
    formEditar.dni = cursante.dni;
    formEditar.fecha_nacimiento = cursante.fecha_nacimiento;
    formEditar.localidad = cursante.localidad;
    formEditar.contacto = cursante.contacto || '';
    formEditar.correo = cursante.correo || '';
    formEditar.escuela = cursante.escuela || '';
    formEditar.nivel_educativo = cursante.nivel_educativo;
    dialogEditar.value = true;
}

function guardarEdicion() {
    loadingEditar.value = true;
    const dataToSend = { ...formEditar, dni: formEditar.dni.trim(), correo: formEditar.correo?.trim() || null };
    window.axios.put(`/standard/cursantes/${formEditar.id}`, dataToSend)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Cursante actualizado correctamente' });
            dialogEditar.value = false;
            cargarCursantes();
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

function confirmarEliminar(cursante) {
    cursanteEliminar.value = cursante;
    dialogEliminar.value = true;
}

function eliminarCursante() {
    loadingEliminar.value = true;
    window.axios.delete(`/standard/cursantes/${cursanteEliminar.value.id}`)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Cursante eliminado correctamente' });
            dialogEliminar.value = false;
            cargarCursantes();
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo eliminar' });
        })
        .finally(() => {
            loadingEliminar.value = false;
        });
}

onMounted(() => {
    cargarCursantes();
});
</script>
