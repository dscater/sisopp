import { onMounted, reactive } from "vue";

const oTarea = reactive({
    id: 0,
    codigo: "",
    nro_cod: "",
    descripcion: "",
    area_id: "",
    producto_id: "",
    user_id: "",
    estado: "",
    tarea_materials: [],
    tarea_operarios: [],
    eliminados_materials: [],
    eliminados_operarios: [],
    _method: "POST",
});

export const useTareas = () => {
    const setTarea = (item = null) => {
        if (item) {
            oTarea.id = item.id;
            oTarea.codigo = "";
            oTarea.nro_cod = "";
            oTarea.descripcion = "";
            oTarea.area_id = "";
            oTarea.producto_id = "";
            oTarea.user_id = "";
            oTarea.estado = "";
            oTarea.tarea_materials = [];
            oTarea.tarea_operarios = [];
            oTarea.eliminados_materials = [];
            oTarea.eliminados_operarios = [];
            oTarea._method = "PUT";
            return oTarea;
        }
        return false;
    };

    const limpiarTarea = () => {
        oTarea.id = 0;
        oTarea.codigo = "";
        oTarea.nro_cod = "";
        oTarea.descripcion = "";
        oTarea.area_id = "";
        oTarea.producto_id = "";
        oTarea.user_id = "";
        oTarea.estado = "";
        oTarea.tarea_materials = [];
        oTarea.tarea_operarios = [];
        oTarea.eliminados_materials = [];
        oTarea.eliminados_operarios = [];
        oTarea._method = "POST";
    };

    onMounted(() => {});

    return {
        oTarea,
        setTarea,
        limpiarTarea,
    };
};
