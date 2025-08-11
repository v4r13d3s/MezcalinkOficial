<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInsignia;
use App\Models\Insignia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class UserInsigniaController extends Controller
{
    /**
     * Obtener todas las insignias de un usuario
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'completada' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            $query = UserInsignia::with(['insignia', 'insignia.categorias_insignias'])
                ->where('user_id', $request->user_id);

            if ($request->has('completada')) {
                $query->where('completada', $request->completada);
            }

            $insignias = $query->orderBy('fecha_obtenida', 'desc')->get();

            return response()->json([
                'message' => 'Insignias del usuario obtenidas correctamente.',
                'status' => 200,
                'data' => [
                    'insignias' => $insignias
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las insignias del usuario.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Iniciar el progreso hacia una insignia
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'insignia_id' => 'required|exists:insignias,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            // Verificar si ya existe el progreso
            $existente = UserInsignia::where('user_id', $request->user_id)
                ->where('insignia_id', $request->insignia_id)
                ->first();

            if ($existente) {
                return response()->json([
                    'message' => 'El usuario ya tiene iniciado el progreso en esta insignia.',
                    'status' => 409,
                    'data' => []
                ], 409);
            }

            // Obtener la insignia para verificar requisitos
            $insignia = Insignia::find($request->insignia_id);
            
            // Verificar si la insignia está activa
            if (!$insignia->activa) {
                return response()->json([
                    'message' => 'La insignia no está activa.',
                    'status' => 400,
                    'data' => []
                ], 400);
            }

            // Crear el progreso
            $userInsignia = UserInsignia::create([
                'user_id' => $request->user_id,
                'insignia_id' => $request->insignia_id,
                'progreso' => 0,
                'porcentaje' => 0,
                'completada' => false,
                'fecha_inicio_progreso' => Carbon::now(),
                'fecha_obtenida' => null,
                'datos_progreso' => [],
                'notas' => null
            ]);

            return response()->json([
                'message' => 'Progreso de insignia iniciado correctamente.',
                'status' => 201,
                'data' => [
                    'user_insignia' => $userInsignia->load('insignia')
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al iniciar el progreso de la insignia.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Obtener el detalle del progreso de una insignia
     */
    public function show(string $id)
    {
        try {
            $userInsignia = UserInsignia::with(['insignia', 'insignia.categorias_insignias', 'user'])
                ->find($id);

            if (!$userInsignia) {
                return response()->json([
                    'message' => 'Progreso de insignia no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'Detalle de progreso de insignia obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'user_insignia' => $userInsignia
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el detalle del progreso de la insignia.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Actualizar el progreso de una insignia
     */
    public function updateProgress(Request $request, string $id)
    {
        try {
            $userInsignia = UserInsignia::find($id);

            if (!$userInsignia) {
                return response()->json([
                    'message' => 'Progreso de insignia no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'progreso' => 'required|integer|min:0',
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

            // Obtener la insignia para calcular el porcentaje
            $insignia = $userInsignia->insignia;
            $porcentaje = ($request->progreso / $insignia->progreso_maximo) * 100;
            $porcentaje = min(100, $porcentaje); // No permitir más del 100%

            // Determinar si se ha completado
            $completada = $porcentaje >= 100;

            // Actualizar el progreso
            $userInsignia->update([
                'progreso' => $request->progreso,
                'porcentaje' => $porcentaje,
                'completada' => $completada,
                'fecha_obtenida' => $completada ? Carbon::now() : null,
                'datos_progreso' => $request->datos_progreso ?? $userInsignia->datos_progreso,
                'notas' => $request->notas ?? $userInsignia->notas
            ]);

            return response()->json([
                'message' => 'Progreso actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'user_insignia' => $userInsignia->load('insignia')
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
     * Otorgar una insignia directamente
     */
    public function award(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'insignia_id' => 'required|exists:insignias,id',
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

            // Verificar si ya tiene la insignia
            $existente = UserInsignia::where('user_id', $request->user_id)
                ->where('insignia_id', $request->insignia_id)
                ->first();

            if ($existente && $existente->completada) {
                return response()->json([
                    'message' => 'El usuario ya tiene esta insignia.',
                    'status' => 409,
                    'data' => []
                ], 409);
            }

            // Si existe pero no está completada, actualizarla
            if ($existente) {
                $existente->update([
                    'progreso' => $existente->insignia->progreso_maximo,
                    'porcentaje' => 100,
                    'completada' => true,
                    'fecha_obtenida' => Carbon::now(),
                    'notas' => $request->notas ?? $existente->notas
                ]);

                $userInsignia = $existente;
            } else {
                // Crear nuevo registro
                $userInsignia = UserInsignia::create([
                    'user_id' => $request->user_id,
                    'insignia_id' => $request->insignia_id,
                    'progreso' => Insignia::find($request->insignia_id)->progreso_maximo,
                    'porcentaje' => 100,
                    'completada' => true,
                    'fecha_inicio_progreso' => Carbon::now(),
                    'fecha_obtenida' => Carbon::now(),
                    'datos_progreso' => [],
                    'notas' => $request->notas
                ]);
            }

            return response()->json([
                'message' => 'Insignia otorgada correctamente.',
                'status' => 200,
                'data' => [
                    'user_insignia' => $userInsignia->load('insignia')
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al otorgar la insignia.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Revocar una insignia
     */
    public function revoke(string $id)
    {
        try {
            $userInsignia = UserInsignia::find($id);

            if (!$userInsignia) {
                return response()->json([
                    'message' => 'Progreso de insignia no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $userInsignia->delete();

            return response()->json([
                'message' => 'Insignia revocada correctamente.',
                'status' => 200,
                'data' => []
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al revocar la insignia.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }
}
