<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useQuasar } from 'quasar';
import { Chart, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend } from 'chart.js';
import { Line, Bar } from 'vue-chartjs';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import * as XLSX from 'xlsx';

// Registrar Chart.js
Chart.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend);

const $q = useQuasar();

// Estados reactivos
const mesSeleccionado = ref(new Date().getMonth() + 1);
const anioSeleccionado = ref(new Date().getFullYear());
const cargando = ref(false);
const contadorActualizacion = ref(0);
const mostrarModalAsistencias = ref(false);

// Datos de reportes
const reporteCursantesTaller = ref([]);
const reporteCursantesSemanales = ref([]);
const reporteAsistencias = ref([]);
const contadores = ref({
    total_cursantes: 0,
    cursantes_mes: 0
});

// Datos para gráficos
const chartCursantesTaller = ref({});
const chartCursantesSemanales = ref({});

// Computed para meses disponibles
const meses = computed(() => {
    return [
        { label: 'Enero', value: 1 },
        { label: 'Febrero', value: 2 },
        { label: 'Marzo', value: 3 },
        { label: 'Abril', value: 4 },
        { label: 'Mayo', value: 5 },
        { label: 'Junio', value: 6 },
        { label: 'Julio', value: 7 },
        { label: 'Agosto', value: 8 },
        { label: 'Septiembre', value: 9 },
        { label: 'Octubre', value: 10 },
        { label: 'Noviembre', value: 11 },
        { label: 'Diciembre', value: 12 },
    ];
});

// Computed para años disponibles
const anios = computed(() => {
    const anioActual = new Date().getFullYear();
    return Array.from({ length: 5 }, (_, i) => anioActual - i);
});

// Computed para ordenar cursantes por taller de mayor a menor
const reporteCursantesTallerOrdenado = computed(() => {
    return [...reporteCursantesTaller.value].sort((a, b) => b.cantidad - a.cantidad);
});

// Computed para ordenar cursantes por semana de mayor a menor
const reporteCursantesSemanadosOrdenado = computed(() => {
    return [...reporteCursantesSemanales.value].sort((a, b) => b.cantidad - a.cantidad);
});

// Computed para ordenar cursantes por semana de forma cronológica
const reporteCursantesSemanasCronologico = computed(() => {
    return [...reporteCursantesSemanales.value].sort((a, b) => {
        return new Date(a.fecha_inicio) - new Date(b.fecha_inicio);
    });
});

// Funciones para cargar datos
const cargarReportes = async () => {
    cargando.value = true;
    try {
        const [resContadores, resCursantesTaller, resCursantesSemanales, resAsistencias] = await Promise.all([
            fetch(`/admin/api/reporte/contadores?mes=${mesSeleccionado.value}&anio=${anioSeleccionado.value}`),
            fetch(`/admin/api/reporte/cursantes-taller-mes?mes=${mesSeleccionado.value}&anio=${anioSeleccionado.value}`),
            fetch(`/admin/api/reporte/cursantes-semana?mes=${mesSeleccionado.value}&anio=${anioSeleccionado.value}`),
            fetch(`/admin/api/reporte/asistencias-cursante`),
        ]);

        const dataContadores = await resContadores.json();
        const dataCursantesTaller = await resCursantesTaller.json();
        const dataCursantesSemanales = await resCursantesSemanales.json();
        const dataAsistencias = await resAsistencias.json();

        contadores.value = dataContadores;
        reporteCursantesTaller.value = dataCursantesTaller.data || [];
        reporteCursantesSemanales.value = dataCursantesSemanales.data || [];
        reporteAsistencias.value = dataAsistencias.data || [];

        actualizarGraficos();
    } catch (error) {
        console.error('Error al cargar reportes:', error);
        $q.notify({
            type: 'negative', message: 'Error al cargar los reportes', position: 'top',
        });
    } finally {
        cargando.value = false;
    }
};

// Función para actualizar gráficos
const actualizarGraficos = () => {
    // Gráfico de cursantes por taller
    chartCursantesTaller.value = {
        labels: reporteCursantesTaller.value.map(r => r.taller),
        datasets: [
            {
                label: 'Cantidad de Cursantes',
                data: reporteCursantesTaller.value.map(r => r.cantidad),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384',
                    '#C9CBCF',
                ],
            },
        ],
    };

    // Gráfico de cursantes por semana
    chartCursantesSemanales.value = {
        labels: reporteCursantesSemanales.value.map(r => r.semana),
        datasets: [
            {
                label: 'Cantidad de Cursantes',
                data: reporteCursantesSemanales.value.map(r => r.cantidad),
                borderColor: '#36A2EB', backgroundColor: 'rgba(54, 162, 235, 0.1)',
                borderWidth: 2, fill: true, tension: 0.4,
            },
        ],
    };

    // Incrementar contador para forzar re-render de gráficos
    contadorActualizacion.value++;
};

// Funciones para exportar a Excel
const exportarTablaExcel = (titulo, datos, columnas) => {
    const ws = XLSX.utils.json_to_sheet(datos.map(fila => {
        const obj = {};
        columnas.forEach(col => {
            obj[col] = fila[col] || '';
        });
        return obj;
    }));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, titulo);
    XLSX.writeFile(wb, `${titulo.toLowerCase().replace(/\s/g, '_')}_${new Date().toISOString().split('T')[0]}.xlsx`);
};

// Exportar a PDF (usando rutas del backend)
const exportarReporteCursantesTallerPDF = () => {
    window.location.href = `/admin/api/reporte/export/cursantes-taller-pdf?mes=${mesSeleccionado.value}&anio=${anioSeleccionado.value}`;
};

const exportarReporteSemanasPDF = () => {
    window.location.href = `/admin/api/reporte/export/cursantes-semanas-pdf?mes=${mesSeleccionado.value}&anio=${anioSeleccionado.value}`;
};

const exportarReporteAsistenciasPDF = () => {
    window.location.href = `/admin/api/reporte/export/asistencias-pdf`;
};

// Exportar a Excel
const exportarReporteCursantesTallerExcel = () => {
    exportarTablaExcel(
        `Cursantes por Taller - ${mesSeleccionado.value}/${anioSeleccionado.value}`,
        reporteCursantesTaller.value,
        ['taller', 'cantidad']
    );
};

const exportarReporteSemanaExcel = () => {
    exportarTablaExcel(
        `Cursantes por Semana - ${mesSeleccionado.value}/${anioSeleccionado.value}`,
        reporteCursantesSemanales.value,
        ['semana', 'cantidad']
    );
};

const exportarReporteAsistenciasExcel = () => {
    exportarTablaExcel(
        `Asistencias de Cursantes`,
        reporteAsistencias.value,
        ['nombre_apellido', 'dni', 'edad', 'localidad', 'asistencias']
    );
};

// Watch para recargar cuando cambia mes o año
watch([mesSeleccionado, anioSeleccionado], () => {
    cargarReportes();
}, { deep: true });

// Cargar datos al montar
onMounted(() => {
    cargarReportes();
});
</script>

<template>
    <AdminLayout>
        <q-page class="q-pa-md">
            <div class="row items-center q-mb-lg">
                <div class="col">
                    <h4 class="q-my-none">Reportes</h4>
                </div>
            </div>

            <!-- Selectores de filtro -->
            <div class="row q-mb-lg q-gutter-md">
                <div class="col-auto">
                    <q-select v-model="mesSeleccionado" :options="meses" option-value="value" option-label="label"
                        label="Seleccionar Mes" emit-value map-options outlined dense class="q-mr-md" />
                </div>
                <div class="col-auto">
                    <q-select v-model="anioSeleccionado" :options="anios" label="Seleccionar Año" outlined dense />
                </div>
            </div>

            <!-- Q-Card de Contadores -->
            <div class="row q-mb-lg">
                <div class="col-12">
                    <q-card>
                        <q-card-section class="row items-center q-gutter-md q-pa-md">
                            <div class="col">
                                <div class="text-h4 text-primary q-mb-xs">{{ contadores.total_cursantes }}</div>
                                <div class="text-caption text-grey-7">Total Cursantes Registrados</div>
                            </div>
                            <q-separator vertical />
                            <div class="col">
                                <div class="text-h4 text-secondary q-mb-xs">{{ contadores.cursantes_mes }}</div>
                                <div class="text-caption text-grey-7">Cursantes en {{ meses.find(m => m.value === mesSeleccionado)?.label }} {{ anioSeleccionado }}</div>
                            </div>
                            <q-separator vertical />
                            <div class="col-auto">
                                <q-btn
                                    color="primary" label="Ver Detalles" icon="visibility"
                                    @click="mostrarModalAsistencias = true" unelevated
                                />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>

            <!-- Card 1: Cursantes por Taller -->
            <div class="row q-col-gutter-lg q-mb-lg">
                <div class="col-12 col-md-6">
                    <q-card>
                        <q-card-section>
                            <div class="text-h6">Cursantes por Taller</div>
                            <div class="text-caption text-grey">Mes: {{ mesSeleccionado }}/{{ anioSeleccionado }}</div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="q-pa-lg">
                            <div v-if="reporteCursantesTaller.length > 0" class="chart-container"
                                style="position: relative; height: 300px;">
                                <Bar :key="`chart-taller-${contadorActualizacion}`" :data="chartCursantesTaller" :options="{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                        },
                                    },
                                }" />
                            </div>
                            <div v-else class="text-center text-grey">
                                <p>Sin datos disponibles</p>
                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section>
                            <div class="text-subtitle2 q-mb-md">Detalle</div>
                            <q-table :rows="reporteCursantesTallerOrdenado" :columns="[
                                { name: 'taller', label: 'Taller', field: 'taller', align: 'left' },
                                { name: 'cantidad', label: 'Cursantes', field: 'cantidad', align: 'center' },
                            ]" row-key="taller" flat bordered dense />
                        </q-card-section>

                        <q-card-section>
                            <div class="row q-gutter-sm">
                                <q-btn color="primary" label="PDF" icon="picture_as_pdf"
                                    @click="exportarReporteCursantesTallerPDF" size="sm" />
                                <q-btn color="green" label="Excel" icon="table_chart"
                                    @click="exportarReporteCursantesTallerExcel" size="sm" />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>

                <!-- Card 2: Cursantes por Semana -->
                <div class="col-12 col-md-6">
                    <q-card>
                        <q-card-section>
                            <div class="text-h6">Cursantes por Semana</div>
                            <div class="text-caption text-grey">Mes: {{ mesSeleccionado }}/{{ anioSeleccionado }}</div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section class="q-pa-lg">
                            <div v-if="reporteCursantesSemanales.length > 0" class="chart-container"
                                style="position: relative; height: 300px;">
                                <Line :key="`chart-semana-${contadorActualizacion}`" :data="chartCursantesSemanales" :options="{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                        },
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                        },
                                    },
                                }" />
                            </div>
                            <div v-else class="text-center text-grey">
                                <p>Sin datos disponibles</p>
                            </div>
                        </q-card-section>

                        <q-separator />

                        <q-card-section>
                            <div class="text-subtitle2 q-mb-md">Detalle por Semana</div>
                            <q-table :rows="reporteCursantesSemanasCronologico" :columns="[
                                { name: 'semana', label: 'Semana', field: 'semana', align: 'left' },
                                { name: 'cantidad', label: 'Cursantes', field: 'cantidad', align: 'center' },
                            ]" row-key="fecha_inicio" flat bordered dense />
                        </q-card-section>

                        <q-card-section>
                            <div class="row q-gutter-sm">
                                <q-btn color="primary" label="PDF" icon="picture_as_pdf"
                                    @click="exportarReporteSemanasPDF" size="sm" />
                                <q-btn color="green" label="Excel" icon="table_chart"
                                    @click="exportarReporteSemanaExcel" size="sm" />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>

            <!-- Modal de Asistencias por Cursante -->
            <q-dialog v-model="mostrarModalAsistencias" full-width full-height>
                <q-card style="max-width: 90vw; max-height: 85vh;">
                    <q-card-section class="row items-center q-pb-none">
                        <div class="text-h6">Asistencias por Cursante</div>
                        <q-space />
                        <q-btn icon="close" flat round dense @click="mostrarModalAsistencias = false" />
                    </q-card-section>

                    <q-card-section class="q-pt-none">
                        <div class="text-caption text-grey">Desde 01 de Enero hasta hoy</div>
                    </q-card-section>

                    <q-separator />

                    <q-card-section style="max-height: 60vh; overflow-y: auto;">
                        <q-table
                            :rows="reporteAsistencias"
                            :columns="[
                                { name: 'nombre_apellido', label: 'Nombre Completo', field: 'nombre_apellido', align: 'left' },
                                { name: 'dni', label: 'DNI', field: 'dni', align: 'center' },
                                { name: 'edad', label: 'Edad', field: 'edad', align: 'center' },
                                { name: 'localidad', label: 'Localidad', field: 'localidad', align: 'left' },
                                { name: 'asistencias', label: 'Asistencias', field: 'asistencias', align: 'center', sortable: true },
                            ]"
                            row-key="dni"
                            flat
                            bordered
                            :rows-per-page-options="[10, 20, 50, 100]"
                            :pagination="{ rowsPerPage: 20, sortBy: 'asistencias', descending: true }"
                        />
                    </q-card-section>

                    <q-separator />

                    <q-card-section>
                        <div class="row q-gutter-sm">
                            <q-btn
                                color="primary"
                                label="Exportar PDF"
                                icon="picture_as_pdf"
                                @click="exportarReporteAsistenciasPDF"
                                unelevated
                            />
                            <q-btn
                                color="green"
                                label="Exportar Excel"
                                icon="table_chart"
                                @click="exportarReporteAsistenciasExcel"
                                unelevated
                            />
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>
        </q-page>
    </AdminLayout>
</template>

<style scoped>
.chart-container {
    width: 100%;
}
</style>
