<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Tarea;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TareaService
{
    private $modulo = "TAREAS";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $tareas = Tarea::select("tareas.*")->get();
        return $tareas;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $tareas = Tarea::with(["tarea_materials", "tarea_operarios"])->select("tareas.*");
        if ($search && trim($search) != '') {
            $tareas->where("nombre", "LIKE", "%$search%");
        }
        $tareas = $tareas->paginate($length, ['*'], 'page', $page);
        return $tareas;
    }

    /**
     * Crear tarea
     *
     * @param array $datos
     * @return Tarea
     */
    public function crear(array $datos): Tarea
    {

        $tarea = Tarea::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA TAREA", $tarea);

        return $tarea;
    }

    /**
     * Actualizar tarea
     *
     * @param array $datos
     * @param Tarea $tarea
     * @return Tarea
     */
    public function actualizar(array $datos, Tarea $tarea): Tarea
    {
        $old_area = Tarea::find($tarea->id);
        $tarea->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA TAREA", $old_area, $tarea);

        return $tarea;
    }

    /**
     * Eliminar tarea
     *
     * @param Tarea $tarea
     * @return boolean
     */
    public function eliminar(Tarea $tarea): bool
    {
        // verificar usos
        $usos = Tarea::where("area_id", $tarea->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }
        // no eliminar tareas predeterminados para el funcionamiento del sistema
        $old_area = Tarea::find($tarea->id);
        $tarea->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA TAREA", $old_area);

        return true;
    }
}
