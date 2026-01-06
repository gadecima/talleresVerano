<template>
    <StandardLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-md">
                <div class="col">
                    <h4 class="q-my-none">{{ isEditing ? 'Editar Cursante' : 'Registrar Cursante' }}</h4>
                    <div class="text-subtitle2 text-grey">Completa los datos del cursante</div>
                </div>
                <div class="col-auto">
                    <q-btn flat icon="arrow_back" label="Volver al dashboard" href="/standard/dashboard" />
                </div>
            </div>

            <q-card>
                <q-card-section>
                    <q-form @submit.prevent="onSubmit" class="q-gutter-md">
                        <div class="row q-col-gutter-md">
                            <div class="col-12 col-md-6">
                                <q-input v-model="form.nombre_apellido" label="Nombre y Apellido" outlined :rules="[valReq]"/>
                            </div>
                            <div class="col-12 col-md-3">
                                <q-input v-model="form.dni" label="DNI" outlined :rules="[valReq, valDni]" mask="########" maxlength="8"/>
                            </div>
                            <div class="col-12 col-md-3">
                                <q-input v-model.number="form.edad" type="number" label="Edad" outlined :rules="[valReq, valEdad]" min="0" max="120" />
                            </div>

                            <div class="col-12 col-md-6">
                                <q-select
                                  v-model="form.localidad"
                                  :options="localidades"
                                  label="Localidad"
                                  outlined
                                  :rules="[valReq]"
                                  emit-value
                                  map-options
                                />
                            </div>
                            <div class="col-12 col-md-6">
                                <q-input v-model="form.tutor" label="Nombre del tutor" outlined :rules="[valReq]"/>
                            </div>
                            <div class="col-12 col-md-6">
                                <q-input v-model="form.contacto" label="Contacto" outlined/>
                            </div>

                            <div class="col-12 col-md-6">
                                <q-input v-model="form.correo" type="email" label="Correo" outlined :rules="[valEmail]"/>
                            </div>
                        </div>

                        <div class="row items-center q-mt-md">
                            <div class="col-auto">
                                <q-btn type="submit" color="primary" :label="isEditing ? 'Actualizar' : 'Guardar'" :loading="loading || loadingForm"/>
                            </div>
                            <div class="col-auto">
                                <q-btn flat :label="isEditing ? 'Restaurar' : 'Limpiar'" @click="resetForm" :disable="loading || loadingForm"/>
                            </div>
                        </div>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-page>
    </StandardLayout>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useQuasar } from 'quasar';
import StandardLayout from '../../Layouts/StandardLayout.vue';

const $q = useQuasar();

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

const form = reactive({
    nombre_apellido: '',
    dni: '',
    edad: null,
    localidad: null,
    tutor: '',
    contacto: '',
    correo: '',
});

const loading = ref(false);
const loadingForm = ref(false);
const isEditing = ref(false);
const cursanteId = ref(null);
const loadedSnapshot = ref(null);

function valReq(v) {
    return (!!v || v === 0) || 'Campo obligatorio';
}

function valEdad(v) {
    if (v === null || v === undefined || v === '') return 'Campo obligatorio';
    const n = Number(v);
    if (Number.isNaN(n)) return 'Debe ser un número';
    if (n < 0 || n > 120) return 'Debe estar entre 0 y 120';
    return true;
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

function resetForm() {
    if (isEditing.value && loadedSnapshot.value) {
        assignForm(loadedSnapshot.value);
        return;
    }
    assignForm({ nombre_apellido: '', dni: '', edad: null, localidad: null, tutor: '', contacto: '', correo: '' });
}

function assignForm(data) {
    form.nombre_apellido = data.nombre_apellido || '';
    form.dni = data.dni || '';
    form.edad = data.edad ?? null;
    form.localidad = data.localidad || null;
    form.tutor = data.tutor || '';
    form.contacto = data.contacto || '';
    form.correo = data.correo || '';
}

function onSubmit() {
    if (loading.value || loadingForm.value) return;
    loading.value = true;
    const payload = {
        ...form,
        edad: form.edad !== null && form.edad !== '' ? Number(form.edad) : null,
        dni: form.dni.trim(),
        tutor: form.tutor.trim(),
        localidad: form.localidad ? form.localidad.toString().trim() : null,
        contacto: form.contacto?.trim() || '',
        correo: form.correo?.trim() || null,
    };

    const request = isEditing.value && cursanteId.value
        ? window.axios.put(`/standard/cursantes/${cursanteId.value}`, payload)
        : window.axios.post('/standard/cursantes', payload);

    request
        .then(() => {
            $q.notify({ type: 'positive', message: isEditing.value ? 'Cursante actualizado correctamente' : 'Cursante registrado correctamente' });
            if (!isEditing.value) {
                setTimeout(() => {
                    window.location.href = `/standard/dashboard?dni=${encodeURIComponent(form.dni)}`;
                }, 800);
            } else {
                loadedSnapshot.value = { ...payload };
            }
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                const first = Object.values(errors).flat()[0];
                $q.notify({ type: 'warning', message: first || 'Revisa los datos del formulario' });
            } else {
                $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo guardar' });
            }
        })
        .finally(() => {
            loading.value = false;
        });
}

function cargarParaEdicion(id) {
    loadingForm.value = true;
    window.axios.get(`/standard/cursantes/${id}`)
        .then(res => {
            assignForm(res.data);
            cursanteId.value = res.data.id;
            isEditing.value = true;
            loadedSnapshot.value = { ...res.data };
        })
        .catch(() => {
            $q.notify({ type: 'negative', message: 'No se pudo cargar el cursante para edición' });
        })
        .finally(() => {
            loadingForm.value = false;
        });
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    const numericId = Number(id);
    if (id && !Number.isNaN(numericId)) {
        cargarParaEdicion(numericId);
    }
});
</script>
