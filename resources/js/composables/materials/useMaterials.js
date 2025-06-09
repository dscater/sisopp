import { onMounted, ref } from "vue";

const oMaterial = ref({
    id: 0,
    nombre: "",
    descripcion: "",
    fecha_registro: "",
    _method: "POST",
});

export const useMaterials = () => {
    const setMaterial = (item = null) => {
        if (item) {
            oMaterial.value.id = item.id;
            oMaterial.value.nombre = item.nombre;
            oMaterial.value.descripcion = item.descripcion;
            oMaterial.value.fecha_registro = item.fecha_registro;
            oMaterial.value._method = "PUT";
            return oMaterial;
        }
        return false;
    };

    const limpiarMaterial = () => {
        oMaterial.value.id = 0;
        oMaterial.value.nombre = "";
        oMaterial.value.descripcion = "";
        oMaterial.value.fecha_registro = "";
        oMaterial.value._method = "POST";
    };

    onMounted(() => {});

    return {
        oMaterial,
        setMaterial,
        limpiarMaterial,
    };
};
