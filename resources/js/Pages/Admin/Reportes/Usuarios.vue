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
    tipo: "todos",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listTipos = ref([
    { value: "todos", label: "TODOS" },
    { value: "ADMINISTRADOR", label: "ADMINISTRADOR" },
    { value: "SUPERVISOR", label: "SUPERVISOR" },
    { value: "OPERARIOS", label: "OPERARIOS" },
]);

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
    <Head title="Reporte Usuarios"></Head>
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Reportes > Usuarios</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Reportes > Usuarios</h1>
    <!-- END page-header -->
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="generarReporte">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Seleccionar tipo de usuario*</label>
                                <select
                                    :hide-details="
                                        form.errors?.tipo ? false : true
                                    "
                                    :error="form.errors?.tipo ? true : false"
                                    :error-messages="
                                        form.errors?.tipo
                                            ? form.errors?.tipo
                                            : ''
                                    "
                                    v-model="form.tipo"
                                    class="form-control"
                                >
                                    <option
                                        v-for="item in listTipos"
                                        :value="item.value"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
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
