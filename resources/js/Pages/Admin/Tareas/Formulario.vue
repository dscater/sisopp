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

const { flash, auth } = usePage().props;

const listAreas = ref([]);
const listProductos = ref([]);
const listMaterials = ref([]);
const listSupervisores = ref([]);
const listOperarios = ref([]);

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nueva Tarea`
        : `<i class="fa fa-edit"></i> Editar Tarea`;
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
                    <form @submit.prevent="enviarFormulario()">
                        <div class="row">
                            <div class="col-12">
                                <label>Descripción de tarea*</label>
                                <el-input
                                    type="textarea"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.descripcion,
                                    }"
                                    v-model="form.descripcion"
                                    autosize
                                ></el-input>
                                <ul
                                    v-if="form.errors?.descripcion"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.descripcion }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar Área de Producción*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'parsley-error': form.errors?.area_id,
                                    }"
                                    v-model="form.area_id"
                                    placeholder="- Seleccione -"
                                    filtereable
                                >
                                    <el-option
                                        v-for="item in listAreas"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>

                                <ul
                                    v-if="form.errors?.area_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.area_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar Producto*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.producto_id,
                                    }"
                                    v-model="form.producto_id"
                                    placeholder="- Seleccione -"
                                    filtereable
                                >
                                    <el-option
                                        v-for="item in listProductos"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.nombre"
                                    ></el-option>
                                </el-select>
                                <ul
                                    v-if="form.errors?.producto_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.producto_id }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label>Seleccionar supervisor*</label>
                                <el-select
                                    class="w-100"
                                    :class="{
                                        'parsley-error': form.errors?.user_id,
                                    }"
                                    placeholder="- Seleccione -"
                                    v-model="form.user_id"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listSupervisores"
                                        :key="item.id"
                                        :value="item.id"
                                        :label="item.full_name"
                                    ></el-option>
                                </el-select>
                                <ul
                                    v-if="form.errors?.user_id"
                                    class="parsley-errors-list filled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.user_id }}
                                    </li>
                                </ul>
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
                                                        <el-select
                                                            class="w-100"
                                                            v-model="
                                                                item.material_id
                                                            "
                                                            placeholder="- Seleccione -"
                                                            filtereable
                                                        >
                                                            <el-option
                                                                v-for="item in listMaterials"
                                                                :key="item.id"
                                                                :value="item.id"
                                                                :label="
                                                                    item.nombre
                                                                "
                                                            ></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td style="width: 10px">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            @click="
                                                                eliminarTareaMaterial(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-trash"
                                                            ></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mt-2"
                                            @click="agregarTareaMaterial"
                                        >
                                            <i class="fa fa-plus"></i> Agregar
                                            material
                                        </button>
                                    </div>
                                </div>
                                <ul
                                    v-if="form.errors?.tarea_materials"
                                    class="parsley-errors-list filled d-block"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.tarea_materials }}
                                    </li>
                                </ul>
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
                                                        <el-select
                                                            class="w-100"
                                                            placeholder="- Seleccione -"
                                                            v-model="
                                                                item.user_id
                                                            "
                                                            filterable
                                                        >
                                                            <el-option
                                                                v-for="item in listOperarios"
                                                                :key="item.id"
                                                                :value="item.id"
                                                                :label="
                                                                    item.full_name
                                                                "
                                                            ></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td style="width: 10px">
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            @click="
                                                                eliminarTareaOperario(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="fa fa-trash"
                                                            ></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary w-100 mt-2"
                                            @click="agregarTareaOperario"
                                        >
                                            <i class="fa fa-plus"></i> Agregar
                                            operario
                                        </button>
                                    </div>
                                </div>
                                <ul
                                    v-if="form.errors?.tarea_operarios"
                                    class="parsley-errors-list filled d-block"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.tarea_operarios }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row" v-if="auth.user.tipo == 'SUPERVISOR'">
                            <div class="col-12">
                                <label>Estado*:</label>
                                <select
                                    class="form-select"
                                    v-model="form.estado"
                                >
                                    <option value="">- Seleccione -</option>
                                    <option value="PENDIENTE">PENDIENTE</option>
                                    <option value="INICIADO">INICIADO</option>
                                    <option value="FINALIZADO">
                                        FINALIZADO
                                    </option>
                                </select>
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
                    <button
                        type="button"
                        @click="enviarFormulario()"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
