<script setup>
import { useApp } from "@/composables/useApp";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useTareas } from "@/composables/tareas/useTareas";
import { useAxios } from "@/composables/axios/useAxios";
import { initDataTable } from "@/composables/datatable.js";
import { ref, onMounted, onBeforeUnmount } from "vue";
import PanelToolbar from "@/Components/PanelToolbar.vue";
// import { useMenu } from "@/composables/useMenu";
import Formulario from "./Formulario.vue";
import Ver from "./Ver.vue";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const { setLoading } = useApp();
onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { setTarea, limpiarTarea } = useTareas();
const { axiosDelete } = useAxios();

const columns = [
    {
        title: "CÓDIGO",
        data: "codigo",
    },
    {
        title: "DESCRIPCIÓN DE TAREA",
        data: "descripcion",
    },
    {
        title: "ÁREA DE PRODUCCIÓN",
        data: "area.nombre",
    },
    {
        title: "PRODUCTO",
        data: "producto.nombre",
    },
    {
        title: "SUPERVISOR",
        data: "supervisor.full_name",
    },
    {
        title: "ESTADO",
        data: "estado",
    },
    {
        title: "FECHA DE REGISTRO",
        data: "fecha_registro_t",
    },
    {
        title: "ACCIONES",
        data: null,
        render: function (data, type, row) {
            let buttons = ``;

            buttons += `<button class="mx-1 rounded-0 btn btn-primary ver" data-id="${row.id}"><i class="fa fa-eye"></i></button>`;
            if (
                props_page.auth?.user.permisos == "*" ||
                props_page.auth?.user.permisos.includes("tareas.edit")
            ) {
                buttons += `<button class="mx-0 rounded-0 btn btn-warning editar" data-id="${row.id}"><i class="fa fa-edit"></i></button>`;
            }

            if (
                props_page.auth?.user.permisos == "*" ||
                props_page.auth?.user.permisos.includes("tareas.destroy")
            ) {
                buttons += ` <button class="mx-0 rounded-0 btn btn-danger eliminar"
                 data-id="${row.id}"
                 data-nombre="${row.codigo}"
                 data-url="${route(
                     "tareas.destroy",
                     row.id
                 )}"><i class="fa fa-trash"></i></button>`;
            }

            return buttons;
        },
    },
];
const loading = ref(false);
const accion_dialog = ref(0);
const open_dialog = ref(false);
const accion_dialog_ver = ref(0);
const open_dialog_ver = ref(false);

const agregarRegistro = () => {
    limpiarTarea();
    accion_dialog.value = 0;
    open_dialog.value = true;
};

const accionesRow = () => {
    // ver
    $("#table-tarea").on("click", "button.ver", function (e) {
        e.preventDefault();
        let id = $(this).attr("data-id");
        axios.get(route("tareas.show", id)).then((response) => {
            setTarea(response.data, true);
            accion_dialog_ver.value = 1;
            open_dialog_ver.value = true;
        });
    });
    // editar
    $("#table-tarea").on("click", "button.editar", function (e) {
        e.preventDefault();
        let id = $(this).attr("data-id");
        axios.get(route("tareas.show", id)).then((response) => {
            setTarea(response.data);
            accion_dialog.value = 1;
            open_dialog.value = true;
        });
    });
    // eliminar
    $("#table-tarea").on("click", "button.eliminar", function (e) {
        e.preventDefault();
        let nombre = $(this).attr("data-nombre");
        let id = $(this).attr("data-id");
        Swal.fire({
            title: "¿Quierés eliminar este registro?",
            html: `<strong>${nombre}</strong>`,
            showCancelButton: true,
            confirmButtonColor: "#B61431",
            confirmButtonText: "Si, eliminar",
            cancelButtonText: "No, cancelar",
            denyButtonText: `No, cancelar`,
        }).then(async (result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                let respuesta = await axiosDelete(route("tareas.destroy", id));
                if (respuesta && respuesta.sw) {
                    updateDatatable();
                }
            }
        });
    });
};

var datatable = null;
var input_search = null;
var debounceTimeout = null;
const loading_table = ref(false);
const datatableInitialized = ref(false);
const updateDatatable = () => {
    datatable.ajax.reload();
};

onMounted(async () => {
    datatable = initDataTable("#table-tarea", columns, route("tareas.api"));
    input_search = document.querySelector('input[type="search"]');

    // Agregar un evento 'keyup' al input de búsqueda con debounce
    input_search.addEventListener("keyup", () => {
        loading_table.value = true;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            datatable.search(input_search.value).draw(); // Realiza la búsqueda manualmente
            loading_table.value = false;
        }, 500);
    });

    datatableInitialized.value = true;
    accionesRow();
});
onBeforeUnmount(() => {
    if (datatable) {
        datatable.clear();
        datatable.destroy(false); // Destruye la instancia del DataTable
        datatable = null;
        datatableInitialized.value = false;
    }
});
</script>
<template>
    <Head title="Tareas"></Head>

    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;">Inicio</a></li>
        <li class="breadcrumb-item active">Tareas</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Tareas</h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN panel -->
            <div class="panel panel-inverse">
                <!-- BEGIN panel-heading -->
                <div class="panel-heading">
                    <h4 class="panel-title btn-nuevo">
                        <button
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'tareas.create'
                                )
                            "
                            type="button"
                            class="btn btn-primary"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo
                        </button>
                    </h4>
                    <!-- <panel-toolbar
                        :mostrar_loading="loading"
                        @loading="updateDatatable"
                    /> -->
                </div>
                <!-- END panel-heading -->
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <table
                        id="table-tarea"
                        width="100%"
                        class="table table-striped table-bordered align-middle text-nowrap tabla_datos"
                    >
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th></th>
                                <th></th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <div class="loading_table" v-show="loading_table">
                            Cargando...
                        </div>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>

    <Formulario
        :open_dialog="open_dialog"
        :accion_dialog="accion_dialog"
        @envio-formulario="updateDatatable"
        @cerrar-dialog="open_dialog = false"
    ></Formulario>

    <Ver
        :open_dialog="open_dialog_ver"
        :accion_dialog="accion_dialog_ver"
        @cerrar-dialog="open_dialog_ver = false"
    ></Ver>
</template>
