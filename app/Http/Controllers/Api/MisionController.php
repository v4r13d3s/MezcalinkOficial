<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mision;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class MisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $misiones = Mision::with(['logo', 'gallery', 'categorias_misiones', 'insignias'])
                ->orderBy('orden')
                ->get();

            if ($misiones->isEmpty()) {
                $data = [
                    'message' => 'No hay misiones registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de misiones obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'misiones' => $misiones
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las misiones.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Detectar si es un array de misiones o una sola misión
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error interno del servidor.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single mision
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorias_misiones_id' => 'nullable|exists:categorias_misiones,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:diaria,semanal,unica,evento,cadena',
            'dificultad' => 'required|in:facil,medio,dificil',
            'tiempo_estimado' => 'nullable|integer',
            'objetivo_descripcion' => 'required|string',
            'objetivo_cantidad' => 'integer|min:1',
            'puntos_experiencia' => 'integer|min:0',
            'puntos_moneda' => 'integer|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'tiempo_limite_horas' => 'nullable|integer|min:1',
            'activa' => 'boolean',
            'visible' => 'boolean',
            'orden' => 'integer',
            'nivel_minimo' => 'integer|min:1',
            'requisitos' => 'nullable|array',
            'metadatos' => 'nullable|array'
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

        // Generar el slug a partir del título
        $slug = Str::slug($request->titulo);

        $mision = Mision::create([
            'categorias_misiones_id' => $request->categorias_misiones_id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'slug' => $slug,
            'tipo' => $request->tipo,
            'dificultad' => $request->dificultad,
            'tiempo_estimado' => $request->tiempo_estimado,
            'objetivo_descripcion' => $request->objetivo_descripcion,
            'objetivo_cantidad' => $request->objetivo_cantidad ?? 1,
            'puntos_experiencia' => $request->puntos_experiencia ?? 0,
            'puntos_moneda' => $request->puntos_moneda ?? 0,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'tiempo_limite_horas' => $request->tiempo_limite_horas,
            'activa' => $request->activa ?? true,
            'visible' => $request->visible ?? true,
            'orden' => $request->orden ?? 0,
            'nivel_minimo' => $request->nivel_minimo ?? 1,
            'requisitos' => $request->requisitos,
            'metadatos' => $request->metadatos
        ]);

        $data = [
            'message' => 'Misión creada correctamente.',
            'status' => 201,
            'data' => [
                'mision' => $mision->load(['categorias_misiones', 'insignias'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple misiones
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.categorias_misiones_id' => 'nullable|exists:categorias_misiones,id',
            '*.titulo' => 'required|string|max:255',
            '*.descripcion' => 'nullable|string',
            '*.tipo' => 'required|in:diaria,semanal,unica,evento,cadena',
            '*.dificultad' => 'required|in:facil,medio,dificil',
            '*.tiempo_estimado' => 'nullable|integer',
            '*.objetivo_descripcion' => 'required|string',
            '*.objetivo_cantidad' => 'integer|min:1',
            '*.puntos_experiencia' => 'integer|min:0',
            '*.puntos_moneda' => 'integer|min:0',
            '*.fecha_inicio' => 'nullable|date',
            '*.fecha_fin' => 'nullable|date|after:fecha_inicio',
            '*.tiempo_limite_horas' => 'nullable|integer|min:1',
            '*.activa' => 'boolean',
            '*.visible' => 'boolean',
            '*.orden' => 'integer',
            '*.nivel_minimo' => 'integer|min:1',
            '*.requisitos' => 'nullable|array',
            '*.metadatos' => 'nullable|array'
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

        DB::beginTransaction();
        
        try {
            $misiones = [];
            foreach ($request->all() as $misionData) {
                $misiones[] = Mision::create([
                    'categorias_misiones_id' => $misionData['categorias_misiones_id'] ?? null,
                    'titulo' => $misionData['titulo'],
                    'descripcion' => $misionData['descripcion'] ?? null,
                    'slug' => Str::slug($misionData['titulo']),
                    'tipo' => $misionData['tipo'],
                    'dificultad' => $misionData['dificultad'],
                    'tiempo_estimado' => $misionData['tiempo_estimado'] ?? null,
                    'objetivo_descripcion' => $misionData['objetivo_descripcion'],
                    'objetivo_cantidad' => $misionData['objetivo_cantidad'] ?? 1,
                    'puntos_experiencia' => $misionData['puntos_experiencia'] ?? 0,
                    'puntos_moneda' => $misionData['puntos_moneda'] ?? 0,
                    'fecha_inicio' => $misionData['fecha_inicio'] ?? null,
                    'fecha_fin' => $misionData['fecha_fin'] ?? null,
                    'tiempo_limite_horas' => $misionData['tiempo_limite_horas'] ?? null,
                    'activa' => $misionData['activa'] ?? true,
                    'visible' => $misionData['visible'] ?? true,
                    'orden' => $misionData['orden'] ?? 0,
                    'nivel_minimo' => $misionData['nivel_minimo'] ?? 1,
                    'requisitos' => $misionData['requisitos'] ?? null,
                    'metadatos' => $misionData['metadatos'] ?? null
                ]);
            }

            DB::commit();

            $data = [
                'message' => count($misiones) . ' misiones creadas correctamente.',
                'status' => 201,
                'data' => [
                    'misiones' => $misiones
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las misiones.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $mision = Mision::with(['logo', 'gallery', 'categorias_misiones', 'insignias', 'progresoUsuarios'])
                ->find($id);

            if (!$mision) {
                $data = [
                    'message' => 'Misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Misión obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'mision' => $mision
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la misión.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $mision = Mision::find($id);

            if (!$mision) {
                $data = [
                    'message' => 'Misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'categorias_misiones_id' => 'nullable|exists:categorias_misiones,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'tipo' => 'required|in:diaria,semanal,unica,evento,cadena',
                'dificultad' => 'required|in:facil,medio,dificil',
                'tiempo_estimado' => 'nullable|integer',
                'objetivo_descripcion' => 'required|string',
                'objetivo_cantidad' => 'integer|min:1',
                'puntos_experiencia' => 'integer|min:0',
                'puntos_moneda' => 'integer|min:0',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'tiempo_limite_horas' => 'nullable|integer|min:1',
                'activa' => 'boolean',
                'visible' => 'boolean',
                'orden' => 'integer',
                'nivel_minimo' => 'integer|min:1',
                'requisitos' => 'nullable|array',
                'metadatos' => 'nullable|array'
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

            $mision->update([
                'categorias_misiones_id' => $request->categorias_misiones_id,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'slug' => Str::slug($request->titulo),
                'tipo' => $request->tipo,
                'dificultad' => $request->dificultad,
                'tiempo_estimado' => $request->tiempo_estimado,
                'objetivo_descripcion' => $request->objetivo_descripcion,
                'objetivo_cantidad' => $request->objetivo_cantidad,
                'puntos_experiencia' => $request->puntos_experiencia,
                'puntos_moneda' => $request->puntos_moneda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'tiempo_limite_horas' => $request->tiempo_limite_horas,
                'activa' => $request->activa,
                'visible' => $request->visible,
                'orden' => $request->orden,
                'nivel_minimo' => $request->nivel_minimo,
                'requisitos' => $request->requisitos,
                'metadatos' => $request->metadatos
            ]);

            $data = [
                'message' => 'Misión actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'mision' => $mision->load(['categorias_misiones', 'insignias'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la misión.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $mision = Mision::withCount(['progresoUsuarios', 'insignias'])->find($id);

            if (!$mision) {
                $data = [
                    'message' => 'Misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene usuarios o insignias asociadas
            if ($mision->progreso_usuarios_count > 0 || $mision->insignias_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar la misión porque tiene usuarios o insignias asociadas.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $mision->delete();

            $data = [
                'message' => 'Misión eliminada correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la misión.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update partially the specified resource in storage.
     */
    public function updatePartial(Request $request, string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $mision = Mision::find($id);

            if (!$mision) {
                $data = [
                    'message' => 'Misión no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('categorias_misiones_id')) {
                $rules['categorias_misiones_id'] = 'nullable|exists:categorias_misiones,id';
            }
            if ($request->has('titulo')) {
                $rules['titulo'] = 'string|max:255';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('tipo')) {
                $rules['tipo'] = 'in:diaria,semanal,unica,evento,cadena';
            }
            if ($request->has('dificultad')) {
                $rules['dificultad'] = 'in:facil,medio,dificil';
            }
            if ($request->has('tiempo_estimado')) {
                $rules['tiempo_estimado'] = 'nullable|integer';
            }
            if ($request->has('objetivo_descripcion')) {
                $rules['objetivo_descripcion'] = 'string';
            }
            if ($request->has('objetivo_cantidad')) {
                $rules['objetivo_cantidad'] = 'integer|min:1';
            }
            if ($request->has('puntos_experiencia')) {
                $rules['puntos_experiencia'] = 'integer|min:0';
            }
            if ($request->has('puntos_moneda')) {
                $rules['puntos_moneda'] = 'integer|min:0';
            }
            if ($request->has('fecha_inicio')) {
                $rules['fecha_inicio'] = 'nullable|date';
            }
            if ($request->has('fecha_fin')) {
                $rules['fecha_fin'] = 'nullable|date|after:fecha_inicio';
            }
            if ($request->has('tiempo_limite_horas')) {
                $rules['tiempo_limite_horas'] = 'nullable|integer|min:1';
            }
            if ($request->has('activa')) {
                $rules['activa'] = 'boolean';
            }
            if ($request->has('visible')) {
                $rules['visible'] = 'boolean';
            }
            if ($request->has('orden')) {
                $rules['orden'] = 'integer';
            }
            if ($request->has('nivel_minimo')) {
                $rules['nivel_minimo'] = 'integer|min:1';
            }
            if ($request->has('requisitos')) {
                $rules['requisitos'] = 'nullable|array';
            }
            if ($request->has('metadatos')) {
                $rules['metadatos'] = 'nullable|array';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ];
                return response()->json($data, 422);
            }

            $updateData = $request->only(array_keys($rules));
            
            if (isset($updateData['titulo'])) {
                $updateData['slug'] = Str::slug($updateData['titulo']);
            }

            $mision->update($updateData);

            $data = [
                'message' => 'Misión actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'mision' => $mision->load(['categorias_misiones', 'insignias'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la misión.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
