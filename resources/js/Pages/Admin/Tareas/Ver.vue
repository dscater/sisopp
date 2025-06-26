<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useTareas } from "@/composables/tareas/useTareas";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
import axios from "axios";
const props = defineProps({
    open_dialog: {
        type: Boolean,
        default: false,
    },
    accion_dialog: {
        type: Number,
        default: 0,
    },
});

const { oTarea, limpiarTarea } = useTareas();
const { axiosGet } = useAxios();
const accion = ref(props.accion_dialog);
const dialog = ref(props.open_dialog);
let form = useForm(oTarea);
watch(
    () => props.open_dialog,
    async (newValue) => {
        dialog.value = newValue;
        if (dialog.value) {
            cargarListas();
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oTarea);
            if (form.id == 0) {
                agregarTareaMaterial();
                agregarTareaOperario();
            }
        }
    }
);
watch(
    () => props.accion_dialog,
    (newValue) => {
        accion.value = newValue;
    }
);

const { flash } = usePage().props;

const listAreas = ref([]);
const listProductos = ref([]);
const listMaterials = ref([]);
const listSupervisores = ref([]);
const listOperarios = ref([]);

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-eye"></i> Ver Tarea`
        : `<i class="fa fa-eye"></i> Ver Tarea`;
});

const enviarFormulario = () => {
    let url =
        form["_method"] == "POST"
            ? route("tareas.store")
            : route("tareas.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            dialog.value = false;
            Swal.fire({
                icon: "success",
                title: "Correcto",
                text: `${flash.bien ? flash.bien : "Proceso realizado"}`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
            limpiarTarea();
            emits("envio-formulario");
        },
        onError: (err) => {
            console.log("ERROR");
            Swal.fire({
                icon: "info",
                title: "Error",
                text: `${
                    flash.error
                        ? flash.error
                        : err.error
                        ? err.error
                        : "Hay errores en el formulario"
                }`,
                confirmButtonColor: "#3085d6",
                confirmButtonText: `Aceptar`,
            });
        },
    });
};

const emits = defineEmits(["cerrar-dialog", "envio-formulario"]);

watch(dialog, (newVal) => {
    if (!newVal) {
        emits("cerrar-dialog");
    }
});

const cerrarDialog = () => {
    dialog.value = false;
    document.getElementsByTagName("body")[0].classList.remove("modal-open");
};

const cargarListas = () => {
    cargarAreas();
    cargarProductos();
    cargarMaterials();
    cargarUsuariosSupervisores();
    cargarUsuariosOperadores();
};

const cargarAreas = () => {
    axios.get(route("areas.listado")).then((response) => {
        listAreas.value = response.data.areas;
    });
};

const cargarProductos = () => {
    axios.get(route("productos.listado")).then((response) => {
        listProductos.value = response.data.productos;
    });
};

const cargarMaterials = () => {
    axios.get(route("materials.listado")).then((response) => {
        listMaterials.value = response.data.materials;
    });
};

const cargarUsuariosSupervisores = async () => {
    const data = await axiosGet(route("usuarios.listado"), {
        tipo: "SUPERVISOR",
    });
    listSupervisores.value = data.usuarios;
};

const cargarUsuariosOperadores = async () => {
    const data = await axiosGet(route("usuarios.listado"), {
        tipo: "OPERARIOS",
    });
    listOperarios.value = data.usuarios;
};

const agregarTareaMaterial = () => {
    form.tarea_materials.push({
        id: 0,
        tarea_id: "",
        material_id: "",
    });
};

const eliminarTareaMaterial = (index) => {
    const item = form.tarea_materials[index];
    if (item.id != 0) {
        form.eliminados_materials.push(item.id);
    }
    form.tarea_materials.splice(index, 1);
};

const agregarTareaOperario = () => {
    form.tarea_operarios.push({
        id: 0,
        tarea_id: "",
        user_id: "",
    });
};

const eliminarTareaOperario = (index) => {
    const item = form.tarea_operarios[index];
    if (item.id != 0) {
        form.eliminados_operarios.push(item.id);
    }
    form.tarea_operarios.splice(index, 1);
};

onMounted(() => {
    cargarListas();
});
</script>

<template>
    <div
        class="modal fade"
        :class="{
            show: dialog,
        }"
        id="modal-dialog-form"
        :style="{
            display: dialog ? 'block' : 'none',
        }"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-orange text-white">
                    <h4 class="modal-title" v-html="tituloDialog"></h4>
                    <button
                        type="button"
                        class="btn-close"
                        @click="cerrarDialog()"
                    ></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <label class="font-weight-bold"
                                    >Descripción de tarea*</label
                                >
                                <p>{{ form.descripcion }}</p>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="font-weight-bold"
                                    >Seleccionar Área de Producción*</label
                                >
                                <p>{{ form.area?.nombre }}</p>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="font-weight-bold"
                                    >Seleccionar Producto*</label
                                >
                                <p>{{ form.producto?.nombre }}</p>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="font-weight-bold"
                                    >Seleccionar supervisor*</label
                                >
                                <p>{{ form.supervisor?.full_name }}</p>
                            </div>
                        </div>
                        <div class="row mt-2 mb-0">
                            <hr class="mb-1" />
                            <div class="col-md-6">
                                <h4 class="w-100 text-center mt-0">
                                    Materiales
                                </h4>
                                <div class="row">
                                    <div
                                        class="col-12"
                                        v-for="(
                                            item, index
                                        ) in form.tarea_materials"
                                    >
                                        <table class="table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        {{
                                                            item.material.nombre
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="w-100 text-center mt-0">
                                    Operarios
                                </h4>
                                <div class="row">
                                    <div
                                        class="col-12"
                                        v-for="(
                                            item, index
                                        ) in form.tarea_operarios"
                                    >
                                        <table class="table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        {{
                                                            item.user.full_name
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label class="font-weight-bold">Estado</label>
                                <p>{{ form.estado }}</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a
                        href="javascript:;"
                        class="btn btn-white"
                        @click="cerrarDialog()"
                        ><i class="fa fa-times"></i> Cerrar</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
