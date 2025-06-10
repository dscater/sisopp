<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useTareas } from "@/composables/tareas/useTareas";
import { useAxios } from "@/composables/axios/useAxios";
import { watch, ref, computed, defineEmits, onMounted, nextTick } from "vue";
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
            document
                .getElementsByTagName("body")[0]
                .classList.add("modal-open");
            form = useForm(oTarea);
            agregarTareaMaterial();
            agregarTareaOperario();
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

const listUsuarios = ref([]);

const tituloDialog = computed(() => {
    return accion.value == 0
        ? `<i class="fa fa-plus"></i> Nueva Área de producción`
        : `<i class="fa fa-edit"></i> Editar Área de producción`;
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
    cargarUsuarios();
};

const cargarUsuarios = async () => {
    const data = await axiosGet(route("usuarios.listado"));
    listUsuarios.value = data.usuarios;
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
                                <select
                                    class="form-select"
                                    :class="{
                                        'parsley-error': form.errors?.area_id,
                                    }"
                                    v-model="form.area_id"
                                >
                                    <option value="">- Seleccione -</option>
                                </select>

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
                                <select
                                    class="form-select"
                                    :class="{
                                        'parsley-error':
                                            form.errors?.producto_id,
                                    }"
                                    v-model="form.producto_id"
                                >
                                    <option value="">- Seleccione -</option>
                                </select>

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
                                <select
                                    class="form-select"
                                    :class="{
                                        'parsley-error': form.errors?.user_id,
                                    }"
                                    v-model="form.user_id"
                                >
                                    <option value="">- Seleccione -</option>
                                </select>
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
                                                        <select
                                                            class="form-select w-100"
                                                            v-model="
                                                                item.material_id
                                                            "
                                                        >
                                                            <option value="">
                                                                - Seleccione -
                                                            </option>
                                                        </select>
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
                                                        <select
                                                            class="form-select w-100"
                                                            v-model="
                                                                item.user_id
                                                            "
                                                        >
                                                            <option value="">
                                                                - Seleccione -
                                                            </option>
                                                        </select>
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
