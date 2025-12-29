<template>
    <StandardLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-md">
                <div class="col">
                    <h4 class="q-my-none">Registrar Cursante</h4>
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
                                <q-input v-model="form.fecha_nacimiento" type="date" label="Fecha de nacimiento" outlined :rules="[valReq]"/>
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
                            <div class="col-12 col-md-3">
                                <q-input v-model="form.contacto" label="Contacto" outlined/>
                            </div>
                            <div class="col-12 col-md-3">
                                <q-input v-model="form.correo" type="email" label="Correo" outlined :rules="[valEmail]"/>
                            </div>

                            <div class="col-12 col-md-6">
                                <q-input v-model="form.escuela" label="Escuela" outlined/>
                            </div>
                            <div class="col-12 col-md-6">
                                <q-select v-model="form.nivel_educativo" :options="niveles" label="Nivel educativo" outlined :rules="[valReq]" emit-value map-options/>
                            </div>
                        </div>

                        <div class="row items-center q-mt-md">
                            <div class="col-auto">
                                <q-btn type="submit" color="primary" label="Guardar" :loading="loading"/>
                            </div>
                            <div class="col-auto">
                                <q-btn flat label="Limpiar" @click="resetForm" :disable="loading"/>
                            </div>
                        </div>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-page>
    </StandardLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useQuasar } from 'quasar';
import StandardLayout from '../../Layouts/StandardLayout.vue';

const $q = useQuasar();

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

const form = reactive({
    nombre_apellido: '',
    dni: '',
    fecha_nacimiento: '',
    localidad: null,
    contacto: '',
    correo: '',
    escuela: '',
    nivel_educativo: null,
});

const loading = ref(false);

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

function resetForm() {
    form.nombre_apellido = '';
    form.dni = '';
    form.fecha_nacimiento = '';
    form.localidad = '';
    form.contacto = '';
    form.correo = '';
    form.escuela = '';
    form.nivel_educativo = null;
}

function onSubmit() {
    loading.value = true;
    // Limpiar espacios en blanco del DNI
    const dataToSend = { ...form, dni: form.dni.trim(), correo: form.correo?.trim() || null };
    window.axios.post('/standard/cursantes', dataToSend)
        .then(() => {
            $q.notify({ type: 'positive', message: 'Cursante registrado correctamente' });
            // Redirigir al dashboard con el DNI como query para precargar la búsqueda
            setTimeout(() => {
              window.location.href = `/standard/dashboard?dni=${encodeURIComponent(form.dni)}`;
            }, 800);
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                const first = Object.values(errors).flat()[0];
                $q.notify({ type: 'warning', message: first || 'Revisa los datos del formulario' });
            } else {
                $q.notify({ type: 'negative', message: err.response?.data?.message || 'No se pudo registrar' });
            }
        })
        .finally(() => {
            loading.value = false;
        });
}
</script>
