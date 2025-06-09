<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    private $modulo = "USUARIOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    /**
     * Obtener nombre de usuario
     *
     * @param string $nom
     * @param string $apep
     * @return string
     */
    public function getNombreUsuario(string $nom, string $apep): string
    {
        //determinando el nombre de usuario inicial del 1er_nombre+apep+tipoUser
        $nombre_user = substr(mb_strtoupper($nom), 0, 1); //inicial 1er_nombre
        $nombre_user .= mb_strtoupper($apep);
        return $nombre_user;
    }

    /**
     * Crear user
     *
     * @param array $datos
     * @return User
     */
    public function crear(array $datos): User
    {
        $user = User::create([
            "usuario" => $datos["correo"],
            "nombre" => mb_strtoupper($datos["nombre"]),
            "paterno" => mb_strtoupper($datos["paterno"]),
            "materno" => mb_strtoupper($datos["materno"]),
            "dir" => mb_strtoupper($datos["dir"]),
            "ci" => $datos["ci"],
            "ci_exp" => $datos["ci_exp"],
            "fono" => $datos["fono"],
            "correo" => $datos["correo"],
            "password" => $datos["ci"],
            "tipo" => $datos["tipo"],
            "acceso" => $datos["acceso"],
            "fecha_registro" => date("Y-m-d")
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN USUARIO", $user);

        return $user;
    }

    /**
     * Actualizar user
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizar(array $datos, User $user): User
    {
        $old_user = User::find($user->id);

        $user->update([
            "usuario" => $datos["correo"],
            "nombre" => mb_strtoupper($datos["nombre"]),
            "paterno" => mb_strtoupper($datos["paterno"]),
            "materno" => mb_strtoupper($datos["materno"]),
            "dir" => mb_strtoupper($datos["dir"]),
            "ci" => $datos["ci"],
            "ci_exp" => $datos["ci_exp"],
            "fono" => $datos["fono"],
            "correo" => $datos["correo"],
            "tipo" => $datos["tipo"],
            "acceso" => $datos["acceso"],
            "fecha_registro" => date("Y-m-d")
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN USUARIO", $old_user, $user->withoutRelations());

        return $user;
    }

    /**
     * Actualizar password
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizarPassword(array $datos, User $user): User
    {
        $user->password = Hash::make($datos["password"]);
        $user->save();
        return $user;
    }

    /**
     * Cargar foto
     *
     * @param User $user
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(User $user, UploadedFile $foto): void
    {
        if ($user->foto) {
            \File::delete(public_path("imgs/users/" . $this->user->foto));
        }

        $nombre = $user->id . time();
        $user->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/users"), $nombre);
        $user->save();
    }

    /**
     * Eliminar user
     *
     * @param User $user
     * @return boolean
     */
    public function eliminar(User $user): bool
    {
        // no eliminar users predeterminados para el funcionamiento del sistema
        $old_user = User::find($user->id);
        $user->status = 0;
        $user->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ AL USUARIO " . $old_user->usuario, $old_user, $user);
        return true;
    }
}
