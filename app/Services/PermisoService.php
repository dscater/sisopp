<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class PermisoService
{
    protected $arrayPermisos = [
        "ADMINISTRADOR" => [
            "usuarios.api",
            "usuarios.index",
            "usuarios.listado",
            "usuarios.create",
            "usuarios.store",
            "usuarios.edit",
            "usuarios.show",
            "usuarios.update",
            "usuarios.destroy",
            "usuarios.password",

            "areas.api",
            "areas.listado",
            "areas.index",
            "areas.create",
            "areas.store",
            "areas.edit",
            "areas.show",
            "areas.update",
            "areas.destroy",

            "materials.api",
            "materials.listado",
            "materials.index",
            "materials.create",
            "materials.store",
            "materials.edit",
            "materials.show",
            "materials.update",
            "materials.destroy",

            "productos.api",
            "productos.listado",
            "productos.index",
            "productos.create",
            "productos.store",
            "productos.edit",
            "productos.show",
            "productos.update",
            "productos.destroy",

            "tareas.api",
            "tareas.listado",
            "tareas.index",
            "tareas.create",
            "tareas.store",
            "tareas.edit",
            "tareas.show",
            "tareas.update",
            "tareas.destroy",

            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.update",
            "configuracions.destroy",

            "reportes.usuarios",
            "reportes.r_usuarios",
            "reportes.tareas",
            "reportes.r_tareas",
        ],
        "SUPERVISOR" => [
            "areas.api",
            "areas.listado",
            "areas.index",
            "areas.create",
            "areas.store",
            "areas.edit",
            "areas.show",
            "areas.update",
            "areas.destroy",

            "materials.api",
            "materials.listado",
            "materials.index",
            "materials.create",
            "materials.store",
            "materials.edit",
            "materials.show",
            "materials.update",
            "materials.destroy",

            "productos.api",
            "productos.listado",
            "productos.index",
            "productos.create",
            "productos.store",
            "productos.edit",
            "productos.show",
            "productos.update",
            "productos.destroy",

            "tareas.api",
            "tareas.listado",
            "tareas.index",
            "tareas.create",
            "tareas.store",
            "tareas.edit",
            "tareas.show",
            "tareas.update",
            "tareas.destroy",
        ],
        "OPERARIOS" => [
            "tareas.api",
            "tareas.listado",
            "tareas.index",
            "tareas.show",
        ],
    ];

    public function getPermisosUser()
    {
        $user = Auth::user();
        $permisos = [];
        if ($user) {
            return $this->arrayPermisos[$user->tipo];
        }

        return $permisos;
    }
}
