<script setup>
import App from "@/Layouts/App.vue";
defineOptions({
    layout: App,
});
import { onMounted, ref, nextTick, computed } from "vue";
import { useApp } from "@/composables/useApp";
// componentes
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import { usePage, Head, Link } from "@inertiajs/vue3";
import Highcharts from "highcharts";
import exporting from "highcharts/modules/exporting";
import accessibility from "highcharts/modules/accessibility";
import DarkUnica from "highcharts/themes/dark-unica";
const { auth } = usePage().props;
const user = ref(auth.user);

DarkUnica(Highcharts);
exporting(Highcharts);
accessibility(Highcharts);
Highcharts.setOptions({
    lang: {
        downloadPNG: "Descargar PNG",
        downloadJPEG: "Descargar JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        printChart: "Imprimir gráfico",
        contextButtonTitle: "Menú de exportación",
        viewFullscreen: "Pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
    },
});

const props_page = defineProps({
    array_infos: {
        type: Array,
    },
});

const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
const { oConfiguracion } = useConfiguracion();

const { props } = usePage();

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Grafico...";
    }
    return "Generar Grafico";
});

const obtenerFechaActual = () => {
    const fecha = new Date();
    const anio = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
    const dia = String(fecha.getDate()).padStart(2, "0"); // Día del mes
    return `${anio}-${mes}-${dia}`;
};

const form = ref({
    estado: "todos",
    fecha_ini: obtenerFechaActual(),
    fecha_fin: obtenerFechaActual(),
});

const listEstados = ref([
    { value: "todos", label: "TODOS" },
    { value: "PENDIENTE", label: "PENDIENTE" },
    { value: "INICIADO", label: "INICIADO" },
    { value: "FINALIZADO", label: "FINALIZADO" },
]);

const generarGrafico = async () => {
    generando.value = true;
    // renderChart(
    //     "container",
    //     ["ÁREA 1", "ÁREA 2", "ÁREA 3", "ÁREA 4"],
    //     [
    //         {
    //             name: "PENDIENTE",
    //             data: [2, 0, 1, 0],
    //         },
    //         {
    //             name: "INICIADO",
    //             data: [0, 1, 0, 1],
    //         },
    //         {
    //             name: "FINALIZADO",
    //             data: [3, 2, 0, 1],
    //         },
    //     ]
    // );
    axios
        .get(route("reportes.rg_tareas"), {
            params: form.value,
        })
        .then((response) => {
            nextTick(() => {
                const containerId = `container`;
                const container = document.getElementById(containerId);
                // Verificar que el contenedor exista y tenga un tamaño válido
                if (container) {
                    renderChart(
                        containerId,
                        response.data.categories,
                        response.data.data
                    );
                } else {
                    console.error(`Contenedor ${containerId} no válido.`);
                }
            });
            // Create the chart
            generando.value = false;
        });
};

const renderChart = (containerId, categories, data) => {
    const rowHeight = 80;
    const minHeight = 200;
    const calculatedHeight = Math.max(minHeight, categories.length * rowHeight);
    Highcharts.chart(containerId, {
        chart: {
            type: "column",
        },
        title: {
            align: "center",
            text: `REPORTE DE TAREAS`,
        },
        subtitle: {
            align: "center",
            text: ``,
        },
        accessibility: {
            announceNewData: {
                enabled: true,
            },
        },
        xAxis: {
            categories: categories,
            scrollbar: {
                enabled: true,
            },
            labels: {
                rotation: -45,
                style: {
                    fontSize: "14px",
                    fontFamily: "Verdana, sans-serif",
                },
            },
            // min: 0,
            // max: 3,
        },
        yAxis: {
            min: 0,
            title: {
                text: "CANTIDAD",
            },
        },
        legend: {
            enabled: true,
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    // format: "{point.y}",
                    style: {
                        fontSize: "14px",
                        fontFamily: "Arial, sans-serif",
                    },
                    formatter: function () {
                        return parseInt(this.point.y); // Aquí se aplica el formato de moneda
                    },
                },
            },
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                console.log(this);
                return `<h4 style="font-size:13px" class="w-100 text-center mb-0">${this.x}</h4><br>
                <h5><strong>${this.series.name}: </strong>${this.point.y}</h5>`;
            },
            style: {
                fontSize: "14px",
                fontFamily: "Arial, sans-serif",
            },
        },

        series: data,
    });
};

onMounted(() => {
    generarGrafico();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});
</script>
<template>
    <Head title="Inicio"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <!-- <h1 class="page-header">Panel</h1> -->
    <!-- END page-header -->
    <div class="row">
        <div class="col-12">
            <h4 class="text-center text-h4">
                Bienvenid@ {{ props.auth.user.full_name }}
            </h4>
        </div>
    </div>
    <div class="row">
        <!-- BEGIN col-3 -->
        <div class="col-xl-3 col-md-6" v-for="item in array_infos">
            <div class="widget widget-stats" :class="[item.color]">
                <div class="stats-icon">
                    <i class="fa" :class="[item.icon]"></i>
                </div>
                <div class="stats-info text-white">
                    <h4>{{ item.label }}</h4>
                    <p>{{ item.cantidad }}</p>
                </div>
                <div class="stats-link">
                    <Link :href="route(item.url)"
                        >Ver más <i class="fa fa-arrow-alt-circle-right"></i
                    ></Link>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    <label>Estado</label>
                    <select
                        class="form-select"
                        v-model="form.estado"
                        @change="generarGrafico"
                    >
                        <option v-for="item in listEstados" :value="item.value">
                            {{ item.label }}
                        </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Fecha inicio</label>
                    <input
                        type="date"
                        class="form-control"
                        v-model="form.fecha_ini"
                        @keyup="generarGrafico"
                    />
                </div>
                <div class="col-md-4">
                    <label>Fecha final</label>
                    <input
                        type="date"
                        class="form-control"
                        v-model="form.fecha_fin"
                        @keyup="generarGrafico"
                    />
                </div>
            </div>
        </div>
        <div class="col-12" id="container"></div>
    </div>
</template>
<style scoped>
.item_btn {
    margin: 10px;
}

.contenido_item i {
    color: black;
}

.contenido_item {
    transition: all 0.8s;
    color: black;
    padding: 10px;
    cursor: pointer;
    background-color: rgb(248, 229, 229);
    border: solid 2px rgb(243, 211, 211);
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    font-size: 1.3em;
    flex-direction: column;
}
</style>
