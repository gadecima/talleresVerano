<template>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-lg">
                <div class="col">
                    <h4 class="q-my-none">Gestión de Usuarios</h4>
                    <div class="text-subtitle2 text-grey">Administra usuarios y roles</div>
                </div>
                <div class="col-auto">
                    <q-btn
                        color="primary"
                        label="Agregar Usuario"
                        icon="add"
                        @click="showUserModal = true"
                    />
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <q-table
                flat
                bordered
                title="Usuarios Registrados"
                :rows="usersList"
                :columns="columns"
                row-key="id"
                class="q-mb-lg"
            >
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn
                            flat
                            dense
                            round
                            icon="edit"
                            @click="editUser(props.row)"
                            color="primary"
                            title="Editar"
                        />
                        <q-btn-dropdown
                            flat
                            dense
                            round
                            icon="security"
                            color="info"
                            title="Cambiar rol"
                        >
                            <q-list>
                                <q-item
                                    v-for="role in rolesList"
                                    :key="role.id"
                                    clickable
                                    v-close-popup
                                    @click="cambiarRol(props.row, role.id)"
                                >
                                    <q-item-section>{{ role.name }}</q-item-section>
                                </q-item>
                            </q-list>
                        </q-btn-dropdown>
                        <q-btn
                            flat
                            dense
                            round
                            icon="delete"
                            @click="deleteUser(props.row.id)"
                            color="negative"
                            title="Eliminar"
                        />
                    </q-td>
                </template>
            </q-table>

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
import { ref, watch, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const $q = useQuasar();

const usersList = ref([]);
const rolesList = ref([]);

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
    { name: 'role', label: 'Rol', field: row => row.role?.name, align: 'left' },
    { name: 'created_at', label: 'Registrado', field: 'created_at', align: 'left' },
    { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' },
];

const refreshUsers = () => {
    fetch('/api/users', { credentials: 'include' })
        .then(response => response.json())
        .then(data => {
            usersList.value = Array.isArray(data) ? data : (data.users || []);
        })
        .catch(error => {
            console.error('Error fetching users:', error);
            $q.notify({ type: 'negative', message: 'Error al cargar los usuarios' });
        });
};

const loadRoles = () => {
    fetch('/api/roles', { credentials: 'include' })
        .then(response => response.json())
        .then(data => { rolesList.value = data || []; })
        .catch(error => {
            console.error('Error fetching roles:', error);
            $q.notify({ type: 'negative', message: 'Error al cargar los roles' });
        });
};

const editUser = (user) => {
    editingUser.value = user;
    formData.value = { name: user.name, email: user.email, role_id: user.role?.id || '', password: '' };
    showUserModal.value = true;
};

const cambiarRol = async (user, roleId) => {
    try {
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
        const index = usersList.value.findIndex(u => u.id === user.id);
        if (index !== -1) usersList.value[index] = updatedUser;

        $q.notify({ type: 'positive', message: `Rol actualizado a ${updatedUser.role.name}` });
    } catch (error) {
        console.error('Error changing role:', error);
        $q.notify({ type: 'negative', message: 'Error al cambiar el rol' });
    }
};

const saveUser = () => {
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
                $q.notify({ type: 'positive', message: 'Usuario actualizado correctamente' });
                showUserModal.value = false;
                editingUser.value = null;
                refreshUsers();
            })
            .catch(error => {
                console.error('Error updating user:', error);
                $q.notify({ type: 'negative', message: 'Error al actualizar usuario' });
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
                $q.notify({ type: 'positive', message: 'Usuario creado correctamente' });
                showUserModal.value = false;
                refreshUsers();
            })
            .catch(error => {
                console.error('Error creating user:', error);
                $q.notify({ type: 'negative', message: 'Error al crear usuario' });
            });
    }
};

const deleteUser = (userId) => {
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
                $q.notify({ type: 'positive', message: 'Usuario eliminado correctamente' });
                refreshUsers();
            })
            .catch(error => {
                console.error('Error deleting user:', error);
                $q.notify({ type: 'negative', message: 'Error al eliminar usuario' });
            });
    });
};

onMounted(() => {
    loadRoles();
    refreshUsers();
});
</script>
