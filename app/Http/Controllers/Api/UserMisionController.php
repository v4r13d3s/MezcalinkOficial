<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMision;
use App\Models\Mision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class UserMisionController extends Controller
{
    /**
     * Obtener todas las misiones de un usuario
     */
    public function index(Request $request)
    {
        try {
            $query = UserMision::with(['mision', 'mision.categorias_misiones', 'user']);

            // Filtrar por usuario si se proporciona user_id
            if ($request->has('user_id')) {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Usuario no válido.',
                        'errors' => $validator->errors(),
                        'status' => 422,
                        'data' => []
                    ], 422);
                }

                $query->where('user_id', $request->user_id);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            $misiones = $query->orderBy('fecha_inicio', 'desc')->get();

            return response()->json([
                'message' => 'Misiones del usuario obtenidas correctamente.',
                'status' => 200,
                'data' => [
                    'misiones' => $misiones
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las misiones del usuario.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Asignar una misión a un usuario
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'mision_id' => 'required|exists:misiones,id',
                'fecha_expiracion' => 'nullable|date|after:now'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            // Verificar si ya existe la asignación
            $existente = UserMision::where('user_id', $request->user_id)
                ->where('mision_id', $request->mision_id)
                ->first();

            if ($existente) {
                return response()->json([
                    'message' => 'El usuario ya tiene asignada esta misión.',
                    'status' => 409,
                    'data' => []
                ], 409);
            }

            // Obtener la misión para verificar requisitos
            $mision = Mision::find($request->mision_id);
            
            // Verificar si la misión está activa
            if (!$mision->activa) {
                return response()->json([
                    'message' => 'La misión no está activa.',
                    'status' => 400,
                    'data' => []
                ], 400);
            }

            // Crear la asignación
            $userMision = UserMision::create([
                'user_id' => $request->user_id,
                'mision_id' => $request->mision_id,
                'estado' => 'pendiente',
                'progreso_actual' => 0,
                'porcentaje' => 0,
                'fecha_inicio' => Carbon::now(),
                'fecha_expiracion' => $request->fecha_expiracion ?? 
                    ($mision->tiempo_limite_horas ? Carbon::now()->addHours($mision->tiempo_limite_horas) : null),
                'intentos' => 0,
                'datos_progreso' => [],
                'notas' => null
            ]);

            return response()->json([
                'message' => 'Misión asignada correctamente.',
                'status' => 201,
                'data' => [
                    'user_mision' => $userMision->load('mision')
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al asignar la misión.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Obtener el detalle de una misión asignada
     */
    public function show(string $id)
    {
        try {
            $userMision = UserMision::with(['mision', 'mision.categorias_misiones', 'user'])
                ->find($id);

            if (!$userMision) {
                return response()->json([
                    'message' => 'Asignación de misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'Detalle de misión asignada obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'user_mision' => $userMision
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el detalle de la misión asignada.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Actualizar el progreso de una misión
     */
    public function updateProgress(Request $request, string $id)
    {
        try {
            $userMision = UserMision::find($id);

            if (!$userMision) {
                return response()->json([
                    'message' => 'Asignación de misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'progreso_actual' => 'required|integer|min:0',
                'datos_progreso' => 'nullable|array',
                'notas' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            // Obtener la misión para calcular el porcentaje
            $mision = $userMision->mision;
            $porcentaje = ($request->progreso_actual / $mision->objetivo_cantidad) * 100;
            $porcentaje = min(100, $porcentaje); // No permitir más del 100%

            // Determinar el estado basado en el progreso
            $estado = $userMision->estado;
            if ($porcentaje >= 100) {
                $estado = 'completada';
            } elseif ($porcentaje > 0) {
                $estado = 'en_progreso';
            }

            // Actualizar el progreso
            $userMision->update([
                'progreso_actual' => $request->progreso_actual,
                'porcentaje' => $porcentaje,
                'estado' => $estado,
                'fecha_completada' => $estado === 'completada' ? Carbon::now() : null,
                'datos_progreso' => $request->datos_progreso ?? $userMision->datos_progreso,
                'notas' => $request->notas ?? $userMision->notas
            ]);

            return response()->json([
                'message' => 'Progreso actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'user_mision' => $userMision->load('mision')
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el progreso.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Marcar una misión como fallida
     */
    public function markAsFailed(string $id)
    {
        try {
            $userMision = UserMision::find($id);

            if (!$userMision) {
                return response()->json([
                    'message' => 'Asignación de misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $userMision->update([
                'estado' => 'fallida',
                'intentos' => $userMision->intentos + 1
            ]);

            return response()->json([
                'message' => 'Misión marcada como fallida.',
                'status' => 200,
                'data' => [
                    'user_mision' => $userMision->load('mision')
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al marcar la misión como fallida.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Reiniciar una misión fallida
     */
    public function restart(string $id)
    {
        try {
            $userMision = UserMision::find($id);

            if (!$userMision) {
                return response()->json([
                    'message' => 'Asignación de misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            if ($userMision->estado !== 'fallida' && $userMision->estado !== 'expirada') {
                return response()->json([
                    'message' => 'Solo se pueden reiniciar misiones fallidas o expiradas.',
                    'status' => 400,
                    'data' => []
                ], 400);
            }

            // Obtener la misión para verificar el tiempo límite
            $mision = $userMision->mision;

            $userMision->update([
                'estado' => 'en_progreso',
                'progreso_actual' => 0,
                'porcentaje' => 0,
                'fecha_inicio' => Carbon::now(),
                'fecha_completada' => null,
                'fecha_expiracion' => $mision->tiempo_limite_horas ? 
                    Carbon::now()->addHours($mision->tiempo_limite_horas) : null,
                'intentos' => $userMision->intentos + 1,
                'datos_progreso' => []
            ]);

            return response()->json([
                'message' => 'Misión reiniciada correctamente.',
                'status' => 200,
                'data' => [
                    'user_mision' => $userMision->load('mision')
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al reiniciar la misión.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Abandonar una misión
     */
    public function abandon(string $id)
    {
        try {
            $userMision = UserMision::find($id);

            if (!$userMision) {
                return response()->json([
                    'message' => 'Asignación de misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            if ($userMision->estado === 'completada') {
                return response()->json([
                    'message' => 'No se puede abandonar una misión completada.',
                    'status' => 400,
                    'data' => []
                ], 400);
            }

            $userMision->delete();

            return response()->json([
                'message' => 'Misión abandonada correctamente.',
                'status' => 200,
                'data' => []
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al abandonar la misión.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }
}
