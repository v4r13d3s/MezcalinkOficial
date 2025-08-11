<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Obtener lista de usuarios
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['logo', 'gallery']);

            // Filtros opcionales
            if ($request->has('nivel')) {
                $query->where('nivel', $request->nivel);
            }
            if ($request->has('tutorial_completado')) {
                $query->where('tutorial_completado', $request->tutorial_completado);
            }

            $users = $query->orderBy('nivel', 'desc')
                          ->orderBy('experiencia_total', 'desc')
                          ->get();

            if ($users->isEmpty()) {
                $data = [
                    'message' => 'No hay usuarios registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de usuarios obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'users' => $users
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los usuarios.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Almacenar un nuevo usuario
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:30',
                'email' => 'required|string|email|max:35|unique:users',
                'password' => 'required|string|min:8',
                'profile_photo_path' => 'nullable|string|max:2048',
                'nivel' => 'integer|min:1',
                'experiencia_total' => 'integer|min:0',
                'experiencia_nivel_actual' => 'integer|min:0',
                'puntos_totales' => 'integer|min:0',
                'monedas' => 'integer|min:0',
                'nivel_anterior' => 'integer|min:1',
                'misiones_completadas' => 'integer|min:0',
                'total_insignias' => 'integer|min:0',
                'racha_dias' => 'integer|min:0',
                'ultima_actividad' => 'nullable|date',
                'fecha_inicio_gamificacion' => 'nullable|date',
                'tutorial_completado' => 'boolean'
            ]);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ];
                return response()->json($data, 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_photo_path' => $request->profile_photo_path,
                'nivel' => $request->nivel ?? 1,
                'experiencia_total' => $request->experiencia_total ?? 0,
                'experiencia_nivel_actual' => $request->experiencia_nivel_actual ?? 0,
                'puntos_totales' => $request->puntos_totales ?? 0,
                'monedas' => $request->monedas ?? 0,
                'nivel_anterior' => $request->nivel_anterior ?? 1,
                'misiones_completadas' => $request->misiones_completadas ?? 0,
                'total_insignias' => $request->total_insignias ?? 0,
                'racha_dias' => $request->racha_dias ?? 0,
                'ultima_actividad' => $request->ultima_actividad ?? now(),
                'fecha_inicio_gamificacion' => $request->fecha_inicio_gamificacion ?? now(),
                'tutorial_completado' => $request->tutorial_completado ?? false
            ]);

            $data = [
                'message' => 'Usuario creado correctamente.',
                'status' => 201,
                'data' => [
                    'user' => $user
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el usuario.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Mostrar un usuario específico
     */
    public function show(string $id)
    {
        try {
            $user = User::with([
                'logo', 
                'gallery',
                'progresoMisiones' => function($query) {
                    $query->where('estado', 'en_progreso');
                },
                'progresoInsignias' => function($query) {
                    $query->where('completada', false);
                }
            ])->find($id);

            if (!$user) {
                $data = [
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Usuario obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'user' => $user
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el usuario.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Actualizar un usuario específico
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                $data = [
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:30',
                'email' => 'string|email|max:35|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'profile_photo_path' => 'nullable|string|max:2048',
                'nivel' => 'integer|min:1',
                'experiencia_total' => 'integer|min:0',
                'experiencia_nivel_actual' => 'integer|min:0',
                'puntos_totales' => 'integer|min:0',
                'monedas' => 'integer|min:0',
                'nivel_anterior' => 'integer|min:1',
                'misiones_completadas' => 'integer|min:0',
                'total_insignias' => 'integer|min:0',
                'racha_dias' => 'integer|min:0',
                'ultima_actividad' => 'nullable|date',
                'fecha_inicio_gamificacion' => 'nullable|date',
                'tutorial_completado' => 'boolean'
            ]);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ];
                return response()->json($data, 422);
            }

            $updateData = $request->except(['password']);
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            $data = [
                'message' => 'Usuario actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'user' => $user->fresh()
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el usuario.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(string $id)
    {
        try {
            $user = User::withCount(['progresoMisiones', 'progresoInsignias'])->find($id);

            if (!$user) {
                $data = [
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Eliminar el usuario (las relaciones se eliminarán automáticamente por las restricciones de clave foránea)
            $user->delete();

            $data = [
                'message' => 'Usuario eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el usuario.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Actualizar la experiencia del usuario
     */
    public function updateExperience(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'experiencia_ganada' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Guardar nivel anterior
                $nivelAnterior = $user->nivel;
                
                // Actualizar experiencia
                $user->experiencia_total += $request->experiencia_ganada;
                $user->experiencia_nivel_actual += $request->experiencia_ganada;

                // Calcular si sube de nivel (ejemplo: 100 exp por nivel)
                $expPorNivel = 100;
                while ($user->experiencia_nivel_actual >= $expPorNivel) {
                    $user->nivel += 1;
                    $user->experiencia_nivel_actual -= $expPorNivel;
                }

                // Si subió de nivel, actualizar nivel_anterior
                if ($user->nivel > $nivelAnterior) {
                    $user->nivel_anterior = $nivelAnterior;
                }

                $user->ultima_actividad = now();
                $user->save();

                DB::commit();

                $data = [
                    'message' => 'Experiencia actualizada correctamente.',
                    'status' => 200,
                    'data' => [
                        'user' => $user,
                        'subio_nivel' => $user->nivel > $nivelAnterior,
                        'niveles_subidos' => $user->nivel - $nivelAnterior
                    ]
                ];

                return response()->json($data, 200);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la experiencia.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Actualizar monedas del usuario
     */
    public function updateCoins(Request $request, string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'cantidad' => 'required|integer',
                'operacion' => 'required|in:sumar,restar'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ], 422);
            }

            if ($request->operacion === 'restar' && $user->monedas < $request->cantidad) {
                return response()->json([
                    'message' => 'El usuario no tiene suficientes monedas.',
                    'status' => 400,
                    'data' => []
                ], 400);
            }

            $user->monedas = $request->operacion === 'sumar' ? 
                $user->monedas + $request->cantidad : 
                $user->monedas - $request->cantidad;
            
            $user->save();

            return response()->json([
                'message' => 'Monedas actualizadas correctamente.',
                'status' => 200,
                'data' => [
                    'user' => $user
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar las monedas.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Actualizar racha de días
     */
    public function updateStreak(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado.',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            $ultimaActividad = $user->ultima_actividad ? Carbon::parse($user->ultima_actividad) : null;
            $hoy = Carbon::today();

            if (!$ultimaActividad || $ultimaActividad->diffInDays($hoy) > 1) {
                // Si no hay última actividad o han pasado más de 1 día, reiniciar racha
                $user->racha_dias = 1;
            } elseif ($ultimaActividad->diffInDays($hoy) === 1) {
                // Si el último acceso fue ayer, incrementar racha
                $user->racha_dias += 1;
            }
            // Si accedió hoy, mantener la racha actual

            $user->ultima_actividad = now();
            $user->save();

            return response()->json([
                'message' => 'Racha actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'user' => $user
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la racha.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }
}
