<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insignia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class InsigniaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $insignias = Insignia::with(['logo', 'gallery', 'categorias_insignias', 'misiones', 'progresoUsuarios'])
                ->orderBy('orden')
                ->get();

            if ($insignias->isEmpty()) {
                $data = [
                    'message' => 'No hay insignias registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de insignias obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'insignias' => $insignias
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las insignias.',
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
            // Detectar si es un array de insignias o una sola insignia
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear la(s) insignia(s).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single insignia
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mision_id' => 'nullable|exists:misiones,id',
            'categorias_insignias_id' => 'nullable|exists:categorias_insignias,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:bronce,plata,oro,especial,evento',
            'rareza' => 'required|in:comun,raro,epico,legendario',
            'puntos' => 'integer|min:0',
            'orden' => 'integer|min:0',
            'progreso_maximo' => 'integer|min:1',
            'activa' => 'boolean',
            'visible' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
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

        // Generar el slug a partir del nombre
        $slug = Str::slug($request->nombre);

        $insignia = Insignia::create([
            'mision_id' => $request->mision_id,
            'categorias_insignias_id' => $request->categorias_insignias_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'slug' => $slug,
            'tipo' => $request->tipo,
            'rareza' => $request->rareza,
            'puntos' => $request->puntos ?? 0,
            'orden' => $request->orden ?? 0,
            'progreso_maximo' => $request->progreso_maximo ?? 1,
            'activa' => $request->activa ?? true,
            'visible' => $request->visible ?? true,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'requisitos' => $request->requisitos,
            'metadatos' => $request->metadatos
        ]);

        $data = [
            'message' => 'Insignia creada correctamente.',
            'status' => 201,
            'data' => [
                'insignia' => $insignia->load(['categorias_insignias', 'misiones'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple insignias
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.mision_id' => 'nullable|exists:misiones,id',
            '*.categorias_insignias_id' => 'nullable|exists:categorias_insignias,id',
            '*.nombre' => 'required|string|max:150',
            '*.descripcion' => 'nullable|string',
            '*.tipo' => 'required|in:bronce,plata,oro,especial,evento',
            '*.rareza' => 'required|in:comun,raro,epico,legendario',
            '*.puntos' => 'integer|min:0',
            '*.orden' => 'integer|min:0',
            '*.progreso_maximo' => 'integer|min:1',
            '*.activa' => 'boolean',
            '*.visible' => 'boolean',
            '*.fecha_inicio' => 'nullable|date',
            '*.fecha_fin' => 'nullable|date|after:fecha_inicio',
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
            $insignias = [];
            foreach ($request->all() as $insigniaData) {
                $insignia = Insignia::create([
                    'mision_id' => $insigniaData['mision_id'] ?? null,
                    'categorias_insignias_id' => $insigniaData['categorias_insignias_id'] ?? null,
                    'nombre' => $insigniaData['nombre'],
                    'descripcion' => $insigniaData['descripcion'] ?? null,
                    'slug' => Str::slug($insigniaData['nombre']),
                    'tipo' => $insigniaData['tipo'],
                    'rareza' => $insigniaData['rareza'],
                    'puntos' => $insigniaData['puntos'] ?? 0,
                    'orden' => $insigniaData['orden'] ?? 0,
                    'progreso_maximo' => $insigniaData['progreso_maximo'] ?? 1,
                    'activa' => $insigniaData['activa'] ?? true,
                    'visible' => $insigniaData['visible'] ?? true,
                    'fecha_inicio' => $insigniaData['fecha_inicio'] ?? null,
                    'fecha_fin' => $insigniaData['fecha_fin'] ?? null,
                    'requisitos' => $insigniaData['requisitos'] ?? null,
                    'metadatos' => $insigniaData['metadatos'] ?? null
                ]);

                $insignias[] = $insignia;
            }

            DB::commit();

            $data = [
                'message' => 'Insignias creadas correctamente.',
                'status' => 201,
                'data' => [
                    'insignias' => $insignias
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las insignias.',
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
            $insignia = Insignia::with(['logo', 'gallery', 'categorias_insignias', 'misiones', 'progresoUsuarios'])
                ->find($id);

            if (!$insignia) {
                $data = [
                    'message' => 'Insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Insignia obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'insignia' => $insignia
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la insignia.',
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
            $insignia = Insignia::find($id);

            if (!$insignia) {
                $data = [
                    'message' => 'Insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'mision_id' => 'nullable|exists:misiones,id',
                'categorias_insignias_id' => 'nullable|exists:categorias_insignias,id',
                'nombre' => 'required|string|max:150',
                'descripcion' => 'nullable|string',
                'tipo' => 'required|in:bronce,plata,oro,especial,evento',
                'rareza' => 'required|in:comun,raro,epico,legendario',
                'puntos' => 'integer|min:0',
                'orden' => 'integer|min:0',
                'progreso_maximo' => 'integer|min:1',
                'activa' => 'boolean',
                'visible' => 'boolean',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
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

            $insignia->update([
                'mision_id' => $request->mision_id,
                'categorias_insignias_id' => $request->categorias_insignias_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'slug' => Str::slug($request->nombre),
                'tipo' => $request->tipo,
                'rareza' => $request->rareza,
                'puntos' => $request->puntos,
                'orden' => $request->orden,
                'progreso_maximo' => $request->progreso_maximo,
                'activa' => $request->activa,
                'visible' => $request->visible,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'requisitos' => $request->requisitos,
                'metadatos' => $request->metadatos
            ]);

            $data = [
                'message' => 'Insignia actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'insignia' => $insignia->load(['categorias_insignias', 'misiones'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la insignia.',
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
            $insignia = Insignia::withCount(['progresoUsuarios'])->find($id);

            if (!$insignia) {
                $data = [
                    'message' => 'Insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene usuarios asociados
            if ($insignia->progreso_usuarios_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar la insignia porque tiene usuarios asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $insignia->delete();

            $data = [
                'message' => 'Insignia eliminada correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la insignia.',
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
            $insignia = Insignia::find($id);

            if (!$insignia) {
                $data = [
                    'message' => 'Insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('mision_id')) {
                $rules['mision_id'] = 'nullable|exists:misiones,id';
            }
            if ($request->has('categorias_insignias_id')) {
                $rules['categorias_insignias_id'] = 'nullable|exists:categorias_insignias,id';
            }
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:150';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('tipo')) {
                $rules['tipo'] = 'in:bronce,plata,oro,especial,evento';
            }
            if ($request->has('rareza')) {
                $rules['rareza'] = 'in:comun,raro,epico,legendario';
            }
            if ($request->has('puntos')) {
                $rules['puntos'] = 'integer|min:0';
            }
            if ($request->has('orden')) {
                $rules['orden'] = 'integer|min:0';
            }
            if ($request->has('progreso_maximo')) {
                $rules['progreso_maximo'] = 'integer|min:1';
            }
            if ($request->has('activa')) {
                $rules['activa'] = 'boolean';
            }
            if ($request->has('visible')) {
                $rules['visible'] = 'boolean';
            }
            if ($request->has('fecha_inicio')) {
                $rules['fecha_inicio'] = 'nullable|date';
            }
            if ($request->has('fecha_fin')) {
                $rules['fecha_fin'] = 'nullable|date|after:fecha_inicio';
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
            
            if (isset($updateData['nombre'])) {
                $updateData['slug'] = Str::slug($updateData['nombre']);
            }

            $insignia->update($updateData);

            $data = [
                'message' => 'Insignia actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'insignia' => $insignia->load(['categorias_insignias', 'misiones'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la insignia.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
