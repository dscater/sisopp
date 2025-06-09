<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Material;
use App\Models\Producto;
use App\Models\Publicacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function permisosUsuario(Request $request)
    {
        return response()->JSON([
            "permisos" => Auth::user()->permisos
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public static function getInfoBoxUser()
    {
        $permisos = [];
        $array_infos = [];
        if (Auth::check()) {
            $oUser = new User();
            $permisos = $oUser->permisos;
            if ($permisos == '*' || (is_array($permisos) && in_array('usuarios.index', $permisos))) {
                $array_infos[] = [
                    'label' => 'USUARIOS',
                    'cantidad' => User::where('id', '!=', 1)->count(),
                    'color' => 'bg-principal',
                    'icon' => "fa-users",
                    "url" => "usuarios.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('areas.index', $permisos))) {
                $areas = Area::select("areas.id");
                $areas = $areas->count();
                $array_infos[] = [
                    'label' => 'ÁREAS DE PRODUCCIÓN',
                    'cantidad' => $areas,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "areas.index"
                ];
            }
            if ($permisos == '*' || (is_array($permisos) && in_array('materials.index', $permisos))) {
                $materials = Material::select("materials.id");
                $materials = $materials->count();
                $array_infos[] = [
                    'label' => 'MATERIALES',
                    'cantidad' => $materials,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "materials.index"
                ];
            }
            if ($permisos == '*' || (is_array($permisos) && in_array('productos.index', $permisos))) {
                $productos = Producto::select("productos.id");
                $productos = $productos->count();
                $array_infos[] = [
                    'label' => 'PRODUCTOS',
                    'cantidad' => $productos,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "productos.index"
                ];
            }
            if ($permisos == '*' || (is_array($permisos) && in_array('tareas.index', $permisos))) {
                $tareas = Tarea::select("tareas.id");
                $tareas = $tareas->count();
                $array_infos[] = [
                    'label' => 'TAREAS',
                    'cantidad' => $tareas,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "tareas.index"
                ];
            }
        }


        return $array_infos;
    }
}
