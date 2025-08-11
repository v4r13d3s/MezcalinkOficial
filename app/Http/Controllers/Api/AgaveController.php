<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agave;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class AgaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $agaves = Agave::with(['logo', 'gallery', 'region', 'mezcals'])
                ->where('activo', true)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get();

            if ($agaves->isEmpty()) {
                $data = [
                    'message' => 'No hay agaves registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de agaves obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'agaves' => $agaves
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los agaves.',
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
            // Detectar si es un array de agaves o un solo agave
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) agave(s).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single agave
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|exists:regions,id',
            'nombre' => 'required|string|max:45',
            'nombre_cientifico' => 'required|string|max:45',
            'usos' => 'nullable|string',
            'altura' => 'nullable|numeric|min:0|max:999.99',
            'diametro' => 'nullable|numeric|min:0|max:999.99',
            'descripcion' => 'nullable|string',
            'tiempo_maduracion' => 'nullable|string|max:50',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0|max:999'
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

        $agave = Agave::create([
            'region_id' => $request->region_id,
            'nombre' => $request->nombre,
            'slug' => $slug,
            'nombre_cientifico' => $request->nombre_cientifico,
            'usos' => $request->usos,
            'altura' => $request->altura,
            'diametro' => $request->diametro,
            'descripcion' => $request->descripcion,
            'tiempo_maduracion' => $request->tiempo_maduracion,
            'activo' => $request->activo ?? true,
            'orden' => $request->orden ?? 0
        ]);

        $data = [
            'message' => 'Agave creado correctamente.',
            'status' => 201,
            'data' => [
                'agave' => $agave->load(['region'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple agaves
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.region_id' => 'nullable|exists:regions,id',
            '*.nombre' => 'required|string|max:45',
            '*.nombre_cientifico' => 'required|string|max:45',
            '*.usos' => 'nullable|string',
            '*.altura' => 'nullable|numeric|min:0|max:999.99',
            '*.diametro' => 'nullable|numeric|min:0|max:999.99',
            '*.descripcion' => 'nullable|string',
            '*.tiempo_maduracion' => 'nullable|string|max:50',
            '*.activo' => 'boolean',
            '*.orden' => 'nullable|integer|min:0|max:999'
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
            $agaves = [];
            foreach ($request->all() as $agaveData) {
                $agave = Agave::create([
                    'region_id' => $agaveData['region_id'] ?? null,
                    'nombre' => $agaveData['nombre'],
                    'slug' => Str::slug($agaveData['nombre']),
                    'nombre_cientifico' => $agaveData['nombre_cientifico'],
                    'usos' => $agaveData['usos'] ?? null,
                    'altura' => $agaveData['altura'] ?? null,
                    'diametro' => $agaveData['diametro'] ?? null,
                    'descripcion' => $agaveData['descripcion'] ?? null,
                    'tiempo_maduracion' => $agaveData['tiempo_maduracion'] ?? null,
                    'activo' => $agaveData['activo'] ?? true,
                    'orden' => $agaveData['orden'] ?? 0
                ]);

                $agaves[] = $agave;
            }

            DB::commit();

            $data = [
                'message' => 'Agaves creados correctamente.',
                'status' => 201,
                'data' => [
                    'agaves' => $agaves
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los agaves.',
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
            $agave = Agave::with(['logo', 'gallery', 'region', 'mezcals'])
                ->find($id);

            if (!$agave) {
                $data = [
                    'message' => 'Agave no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Agave obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'agave' => $agave
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el agave.',
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
            $agave = Agave::find($id);

            if (!$agave) {
                $data = [
                    'message' => 'Agave no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'region_id' => 'nullable|exists:regions,id',
                'nombre' => 'required|string|max:45',
                'nombre_cientifico' => 'required|string|max:45',
                'usos' => 'nullable|string',
                'altura' => 'nullable|numeric|min:0|max:999.99',
                'diametro' => 'nullable|numeric|min:0|max:999.99',
                'descripcion' => 'nullable|string',
                'tiempo_maduracion' => 'nullable|string|max:50',
                'activo' => 'boolean',
                'orden' => 'nullable|integer|min:0|max:999'
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

            $agave->update([
                'region_id' => $request->region_id,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'nombre_cientifico' => $request->nombre_cientifico,
                'usos' => $request->usos,
                'altura' => $request->altura,
                'diametro' => $request->diametro,
                'descripcion' => $request->descripcion,
                'tiempo_maduracion' => $request->tiempo_maduracion,
                'activo' => $request->activo,
                'orden' => $request->orden
            ]);

            $data = [
                'message' => 'Agave actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'agave' => $agave->load(['region'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el agave.',
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
            $agave = Agave::withCount(['mezcals'])->find($id);

            if (!$agave) {
                $data = [
                    'message' => 'Agave no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($agave->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar el agave porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $agave->delete();

            $data = [
                'message' => 'Agave eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el agave.',
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
            $agave = Agave::find($id);

            if (!$agave) {
                $data = [
                    'message' => 'Agave no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('region_id')) {
                $rules['region_id'] = 'nullable|exists:regions,id';
            }
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:45';
            }
            if ($request->has('nombre_cientifico')) {
                $rules['nombre_cientifico'] = 'string|max:45';
            }
            if ($request->has('usos')) {
                $rules['usos'] = 'nullable|string';
            }
            if ($request->has('altura')) {
                $rules['altura'] = 'nullable|numeric|min:0|max:999.99';
            }
            if ($request->has('diametro')) {
                $rules['diametro'] = 'nullable|numeric|min:0|max:999.99';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('tiempo_maduracion')) {
                $rules['tiempo_maduracion'] = 'nullable|string|max:50';
            }
            if ($request->has('activo')) {
                $rules['activo'] = 'boolean';
            }
            if ($request->has('orden')) {
                $rules['orden'] = 'nullable|integer|min:0|max:999';
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

            $agave->update($updateData);

            $data = [
                'message' => 'Agave actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'agave' => $agave->load(['region'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el agave.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
