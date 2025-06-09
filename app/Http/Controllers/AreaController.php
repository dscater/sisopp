<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaStoreRequest;
use App\Http\Requests\AreaUpdateRequest;
use App\Models\Area;
use App\Services\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AreaController extends Controller
{
    public function __construct(private AreaService $areaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Areas/Index");
    }

    /**
     * Listado de areas
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "areas" => $this->areaService->listado()
        ]);
    }

    /**
     * Listado de areas para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "areas" => $this->areaService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de areas paginado para datatable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {

        $length = (int)$request->input('length', 10); // Valor de `length` enviado por DataTable
        $start = (int)$request->input('start', 0); // Índice de inicio enviado por DataTable
        $page = (int)(($start / $length) + 1); // Cálculo de la página actual
        $search = (string)$request->input('search', '');

        $usuarios = $this->areaService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo area
     *
     * @param AreaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(AreaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Area
            $this->areaService->crear($request->validated());
            DB::commit();
            return redirect()->route("areas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un area
     *
     * @param Area $area
     * @return JsonResponse
     */
    public function show(Area $area): JsonResponse
    {
        return response()->JSON($area);
    }

    public function update(Area $area, AreaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar area
            $this->areaService->actualizar($request->validated(), $area);
            DB::commit();
            return redirect()->route("areas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar area
     *
     * @param Area $area
     * @return JsonResponse|Response
     */
    public function destroy(Area $area): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->areaService->eliminar($area);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
