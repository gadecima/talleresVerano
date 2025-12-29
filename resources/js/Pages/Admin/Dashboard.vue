<template>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-lg">
                <div class="col">
                    <h4 class="q-my-none">Dashboard Administrador</h4>
                </div>
            </div>

            <!-- Cards de acceso rápido -->
            <div class="row q-col-gutter-md q-mb-lg">
                <!-- Card de Usuarios -->
                <div class="col-12 col-md-6">
                    <q-card>
                        <q-card-section>
                            <div class="row items-center">
                                <q-icon name="people" size="48px" color="primary" class="q-mr-md" />
                                <div class="col">
                                    <div class="text-h6">Gestión de Usuarios</div>
                                    <div class="text-subtitle2 text-grey">Administra usuarios y roles</div>
                                </div>
                                <q-btn
                                    color="primary"
                                    label="Ver Usuarios"
                                    icon-right="arrow_forward"
                                    href="/admin/section1"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>

                <!-- Card de Talleres -->
                <div class="col-12 col-md-6">
                    <q-card>
                        <q-card-section>
                            <div class="row items-center">
                                <q-icon name="school" size="48px" color="primary" class="q-mr-md" />
                                <div class="col">
                                    <div class="text-h6">Gestión de Talleres</div>
                                    <div class="text-subtitle2 text-grey">Administra los talleres del sistema</div>
                                </div>
                                <q-btn
                                    color="primary"
                                    label="Ver Talleres"
                                    icon-right="arrow_forward"
                                    href="/admin/section2"
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>

            <!-- Tabla de Histórico de Inscripciones -->
            <q-card>
                <q-card-section>
                    <div class="row items-center q-mb-md">
                        <div class="col-12 col-md-6">
                            <div class="text-h6 q-mb-sm">Histórico de Inscripciones</div>
                            <q-input v-model="searchInscripciones" label="Buscar por cursante o taller" outlined dense clearable
                                @update:model-value="buscarInscripcionesConDebounce">
                                <template v-slot:prepend>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-auto q-ml-auto">
                            <q-btn flat icon="refresh" label="Actualizar" @click="cargarInscripciones" :loading="loadingInscripciones" />
                        </div>
                    </div>

                    <q-table
                        :rows="inscripciones"
                        :columns="inscripcionesColumns"
                        row-key="id"
                        v-model:pagination="inscripcionesPagination"
                        @request="onRequestInscripciones"
                        :loading="loadingInscripciones"
                        flat
                        bordered
                    >
                        <template v-slot:body-cell-fecha="props">
                            <q-td :props="props">
                                {{ new Date(props.row.fecha).toLocaleDateString('es-ES') }}
                            </q-td>
                        </template>
                    </q-table>
                </q-card-section>
            </q-card>

            <!-- Modal para Crear/Editar Usuario -->
            <q-dialog v-model="showUserModal">
                <q-card style="min-width: 400px">
                    <q-card-section class="row items-center q-pb-none">
                        <div class="text-h6">{{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}</div>
                        <q-space />
                        <q-btn icon="close" flat round dense @click="showUserModal = false" />
                    </q-card-section>

                    <q-card-section>
                        <q-form @submit="saveUser">
                            <q-input
                                v-model="formData.name"
                                label="Nombre"
                                outlined
                                dense
                                class="q-mb-md"
                            />
                            <q-input
                                v-model="formData.email"
                                label="Email"
                                type="email"
                                outlined
                                dense
                                class="q-mb-md"
                            />
                            <q-select
                                v-model="formData.role_id"
                                :options="rolesList"
                                option-value="id"
                                option-label="name"
                                label="Rol"
                                outlined
                                dense
                                class="q-mb-md"
                            />
                            <q-input
                                v-if="!editingUser"
                                v-model="formData.password"
                                label="Contraseña"
                                type="password"
                                outlined
                                dense
                                class="q-mb-md"
                            />
                            <div class="row q-gutter-md">
                                <q-btn label="Guardar" type="submit" color="primary" />
                                <q-btn label="Cancelar" @click="showUserModal = false" />
                            </div>
                        </q-form>
                    </q-card-section>
                </q-card>
            </q-dialog>
        </q-page>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import AdminLayout from '../../Layouts/AdminLayout.vue';

// Props que llegan desde el servidor (Inertia)
const props = defineProps({
    users: Array,
    roles: Array,
});

const $q = useQuasar();

// Refs locales que se inicializan con los props
const usersList = ref(props.users || []);
const rolesList = ref(props.roles || []);

// Refs para inscripciones
const inscripciones = ref([]);
const loadingInscripciones = ref(false);
const searchInscripciones = ref('');
let debounceTimerInscripciones = null;

const inscripcionesPagination = ref({
    sortBy: 'fecha',
    descending: true,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0,
});

const inscripcionesColumns = [
    { name: 'cursante', label: 'Cursante', field: row => row.cursante?.nombre_apellido, align: 'left', sortable: true },
    { name: 'dni', label: 'DNI', field: row => row.cursante?.dni, align: 'left' },
    { name: 'taller', label: 'Taller', field: row => row.taller?.nombre, align: 'left', sortable: true },
    { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left', sortable: true },
    { name: 'estado', label: 'Estado', field: 'estado', align: 'center' },
];

// Observar cambios en los props
watch(() => props.users, (newUsers) => {
    usersList.value = newUsers || [];
});

watch(() => props.roles, (newRoles) => {
    rolesList.value = newRoles || [];
});

const showUserModal = ref(false);
const editingUser = ref(null);

const formData = ref({
    name: '',
    email: '',
    role_id: '',
    password: '',
});

const columns = [
    { name: 'id', label: 'ID', field: 'id', align: 'left' },
    { name: 'name', label: 'Nombre', field: 'name', align: 'left' },
    { name: 'email', label: 'Email', field: 'email', align: 'left' },
    { name: 'role', label: 'Rol', field: row => row.role.name, align: 'left' },
    { name: 'created_at', label: 'Registrado', field: 'created_at', align: 'left' },
    { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
];

// Funciones para inscripciones
function buscarInscripcionesConDebounce() {
    clearTimeout(debounceTimerInscripciones);
    debounceTimerInscripciones = setTimeout(() => {
        inscripcionesPagination.value.page = 1;
        cargarInscripciones();
    }, 500);
}

function onRequestInscripciones(props) {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    inscripcionesPagination.value.page = page;
    inscripcionesPagination.value.rowsPerPage = rowsPerPage;
    inscripcionesPagination.value.sortBy = sortBy;
    inscripcionesPagination.value.descending = descending;
    cargarInscripciones();
}

function cargarInscripciones() {
    loadingInscripciones.value = true;
    const params = {
        page: inscripcionesPagination.value.page,
        per_page: inscripcionesPagination.value.rowsPerPage,
    };
    if (searchInscripciones.value) {
        params.search = searchInscripciones.value;
    }

    window.axios.get('/admin/inscripciones', { params })
        .then(res => {
            inscripciones.value = res.data.data;
            inscripcionesPagination.value.rowsNumber = res.data.total;
        })
        .catch(err => {
            $q.notify({ type: 'negative', message: 'Error al cargar inscripciones' });
        })
        .finally(() => {
            loadingInscripciones.value = false;
        });
}

// Los datos ya están disponibles desde los props del servidor
// No necesitamos hacer llamadas API para carga inicial
const refreshUsers = () => {
    fetch('/api/users')
        .then(response => response.json())
        .then(data => {
            usersList.value = data.users || data;
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            $q.notify({
                type: 'negative',
                message: 'Error al cargar los usuarios',
            });
        });
};

const editUser = (user) => {
    editingUser.value = user;
    formData.value = { ...user };
    showUserModal.value = true;
};

const cambiarRol = async (user, roleId) => {
    try {
        // Obtener token XSRF de la cookie
        const token = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];

        const response = await fetch(`/api/users/${user.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(token || ''),
            },
            credentials: 'include',
            body: JSON.stringify({ role_id: roleId }),
        });

        if (!response.ok) throw new Error('Error al actualizar');

        const updatedUser = await response.json();

        // Actualizar solo ese usuario en la tabla
        const index = usersList.value.findIndex(u => u.id === user.id);
        if (index !== -1) {
            usersList.value[index] = updatedUser;
        }

        $q.notify({
            type: 'positive',
            message: `Rol actualizado a ${updatedUser.role.name}`,
            position: 'top-right',
        });
    } catch (error) {
        console.error('Error changing role:', error);
        $q.notify({
            type: 'negative',
            message: 'Error al cambiar el rol',
            position: 'top-right',
        });
    }
};

const saveUser = () => {
    // Obtener token XSRF de la cookie
    const token = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
    const xsrfToken = decodeURIComponent(token || '');

    if (editingUser.value) {
        fetch(`/api/users/${editingUser.value.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrfToken,
            },
            credentials: 'include',
            body: JSON.stringify(formData.value),
        })
            .then(response => response.json())
            .then(() => {
                $q.notify({
                    type: 'positive',
                    message: 'Usuario actualizado correctamente',
                });
                showUserModal.value = false;
                refreshUsers();
            })
            .catch(error => {
                console.error('Error updating user:', error);
                $q.notify({
                    type: 'negative',
                    message: 'Error al actualizar usuario',
                });
            });
    } else {
        fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrfToken,
            },
            credentials: 'include',
            body: JSON.stringify(formData.value),
        })
            .then(response => response.json())
            .then(() => {
                $q.notify({
                    type: 'positive',
                    message: 'Usuario creado correctamente',
                });
                showUserModal.value = false;
                refreshUsers();
            })
            .catch(error => {
                console.error('Error creating user:', error);
                $q.notify({
                    type: 'negative',
                    message: 'Error al crear usuario',
                });
            });
    }
};

const deleteUser = (userId) => {
    // Obtener token XSRF de la cookie
    const token = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1];
    const xsrfToken = decodeURIComponent(token || '');

    $q.dialog({
        title: 'Confirmar eliminación',
        message: '¿Está seguro que desea eliminar este usuario?',
        cancel: true,
        persistent: true,
    }).onOk(() => {
        fetch(`/api/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': xsrfToken,
            },
            credentials: 'include',
        })
            .then(response => response.json())
            .then(() => {
                $q.notify({
                    type: 'positive',
                    message: 'Usuario eliminado correctamente',
                });
                refreshUsers();
            })
            .catch(error => {
                console.error('Error deleting user:', error);
                $q.notify({
                    type: 'negative',
                    message: 'Error al eliminar usuario',
                });
            });
    });
};

onMounted(() => {
    cargarInscripciones();
});
</script>
