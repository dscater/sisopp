<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Usuarios",
        disabled: false,
        url: "",
        name_url: "",
    },
];
</script>

<script setup>
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

const { setLoading } = useApp();

const cargarListas = () => {};

const listSucursals = ref([]);

onMounted(() => {
    cargarListas();
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const form = ref({
    area_id: "todos",
    estado: "todos",
    fecha_ini: "",
    fecha_fin: "",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listAreas = ref([{ value: "todos", label: "TODOS" }]);
const listEstados = ref([{ value: "todos", label: "TODOS" }]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_usuarios", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};
</script>
<template>
    <Head title="Reporte Informe de Tareas"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Informe de Tareas</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Informe de Tareas</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Seleccionar Área de producción*</label>
                                <select
                                    :hide-details="
                                        form.errors?.area_id ? false : true
                                    "
                                    :error="form.errors?.area_id ? true : false"
                                    :error-messages="
                                        form.errors?.area_id
                                            ? form.errors?.area_id
                                            : ''
                                    "
                                    v-model="form.area_id"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listAreas"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label>Seleccionar Estado*</label>
                                <select
                                    :hide-details="
                                        form.errors?.estado ? false : true
                                    "
                                    :error="form.errors?.estado ? true : false"
                                    :error-messages="
                                        form.errors?.estado
                                            ? form.errors?.estado
                                            : ''
                                    "
                                    v-model="form.estado"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listAreas"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Fecha inicio*</label>
                                        <input
                                            type="date"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Fecha fin*</label>
                                        <input
                                            type="date"
                                            class="form-control"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-3">
                                <button
                                    class="btn btn-primary"
                                    block
                                    @click="generarReporte"
                                    :disabled="generando"
                                    v-text="txtBtn"
                                ></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
