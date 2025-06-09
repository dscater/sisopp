import { onMounted, ref } from "vue";

const oTarea = ref({
    id: 0,
    nombre: "",
    descripcion: "",
    fecha_registro: "",
    _method: "POST",
});

export const useTareas = () => {
    const setTarea = (item = null) => {
        if (item) {
            oTarea.value.id = item.id;
            oTarea.value.nombre = item.nombre;
            oTarea.value.descripcion = item.descripcion;
            oTarea.value.fecha_registro = item.fecha_registro;
            oTarea.value._method = "PUT";
            return oTarea;
        }
        return false;
    };

    const limpiarTarea = () => {
        oTarea.value.id = 0;
        oTarea.value.nombre = "";
        oTarea.value.descripcion = "";
        oTarea.value.fecha_registro = "";
        oTarea.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oTarea,
        setTarea,
        limpiarTarea,
    };
};
