<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Producto;
use App\Models\Tmaterial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductoService
{
    private $modulo = "PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $materials = Producto::select("materials.*")->get();
        return $materials;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $materials = Producto::select("materials.*");
        if ($search && trim($search) != '') {
            $materials->where("nombre", "LIKE", "%$search%");
        }
        $materials = $materials->paginate($length, ['*'], 'page', $page);
        return $materials;
    }

    /**
     * Crear material
     *
     * @param array $datos
     * @return Producto
     */
    public function crear(array $datos): Producto
    {

        $material = Producto::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PRODUCTO", $material);

        return $material;
    }

    /**
     * Actualizar material
     *
     * @param array $datos
     * @param Producto $material
     * @return Producto
     */
    public function actualizar(array $datos, Producto $material): Producto
    {
        $old_material = Producto::find($material->id);
        $material->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PRODUCTO", $old_material, $material);

        return $material;
    }

    /**
     * Eliminar material
     *
     * @param Producto $material
     * @return boolean
     */
    public function eliminar(Producto $material): bool
    {
        // verificar usos
        $usos = Tmaterial::where("material_id", $material->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }
        // no eliminar materials predeterminados para el funcionamiento del sistema
        $old_material = Producto::find($material->id);
        $material->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PRODUCTO", $old_material);

        return true;
    }
}
