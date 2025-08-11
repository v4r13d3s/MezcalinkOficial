<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mezcal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class MezcalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $mezcals = Mezcal::with([
                'logo', 
                'gallery', 
                'regions', 
                'marcas', 
                'tipo_maduracion', 
                'categoria_mezcal', 
                'tipo_elaboracion', 
                'maestros', 
                'palenques', 
                'agaves'
            ])
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            if ($mezcals->isEmpty()) {
                $data = [
                    'message' => 'No hay mezcales registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de mezcales obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'mezcals' => $mezcals
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los mezcales.',
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
            // Detectar si es un array de mezcales o un solo mezcal
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) mezcal(es).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single mezcal
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|exists:regions,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'tipo_elaboracion_id' => 'nullable|exists:tipo_elaboracion,id',
            'categoria_mezcal_id' => 'nullable|exists:categoria_mezcal,id',
            'tipo_maduracion_id' => 'nullable|exists:tipo_maduracion,id',
            'maestro_id' => 'nullable|exists:maestros,id',
            'palenque_id' => 'nullable|exists:palenques,id',
            'nombre' => 'required|string|max:100',
            'precio_regular' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'contenido_alcohol' => 'nullable|numeric|min:0|max:100',
            'tamanio_bote' => 'nullable|string|max:50',
            'proveedor' => 'nullable|string|max:100',
            'notas_cata' => 'nullable|string',
            'premios' => 'nullable|array',
            'agave_ids' => 'nullable|array',
            'agave_ids.*' => 'exists:agaves,id',
            'activo' => 'boolean'
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

        DB::beginTransaction();
        
        try {
            $mezcal = Mezcal::create([
                'region_id' => $request->region_id,
                'marca_id' => $request->marca_id,
                'tipo_elaboracion_id' => $request->tipo_elaboracion_id,
                'categoria_mezcal_id' => $request->categoria_mezcal_id,
                'tipo_maduracion_id' => $request->tipo_maduracion_id,
                'maestro_id' => $request->maestro_id,
                'palenque_id' => $request->palenque_id,
                'nombre' => $request->nombre,
                'slug' => $slug,
                'precio_regular' => $request->precio_regular,
                'descripcion' => $request->descripcion,
                'contenido_alcohol' => $request->contenido_alcohol,
                'tamanio_bote' => $request->tamanio_bote,
                'proveedor' => $request->proveedor,
                'notas_cata' => $request->notas_cata,
                'premios' => $request->premios,
                'activo' => $request->activo ?? true
            ]);

            // Asociar agaves si se proporcionan
            if ($request->has('agave_ids') && is_array($request->agave_ids)) {
                $mezcal->agaves()->attach($request->agave_ids);
            }

            DB::commit();

            $data = [
                'message' => 'Mezcal creado correctamente.',
                'status' => 201,
                'data' => [
                    'mezcal' => $mezcal->load([
                        'regions', 
                        'marcas', 
                        'tipo_maduracion', 
                        'categoria_mezcal', 
                        'tipo_elaboracion', 
                        'maestros', 
                        'palenques', 
                        'agaves'
                    ])
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store multiple mezcals
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.region_id' => 'nullable|exists:regions,id',
            '*.marca_id' => 'nullable|exists:marcas,id',
            '*.tipo_elaboracion_id' => 'nullable|exists:tipo_elaboracion,id',
            '*.categoria_mezcal_id' => 'nullable|exists:categoria_mezcal,id',
            '*.tipo_maduracion_id' => 'nullable|exists:tipo_maduracion,id',
            '*.maestro_id' => 'nullable|exists:maestros,id',
            '*.palenque_id' => 'nullable|exists:palenques,id',
            '*.nombre' => 'required|string|max:100',
            '*.precio_regular' => 'nullable|numeric|min:0',
            '*.descripcion' => 'nullable|string',
            '*.contenido_alcohol' => 'nullable|numeric|min:0|max:100',
            '*.tamanio_bote' => 'nullable|string|max:50',
            '*.proveedor' => 'nullable|string|max:100',
            '*.notas_cata' => 'nullable|string',
            '*.premios' => 'nullable|array',
            '*.agave_ids' => 'nullable|array',
            '*.agave_ids.*' => 'exists:agaves,id',
            '*.activo' => 'boolean'
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
            $mezcals = [];
            foreach ($request->all() as $mezcalData) {
                $mezcal = Mezcal::create([
                    'region_id' => $mezcalData['region_id'] ?? null,
                    'marca_id' => $mezcalData['marca_id'] ?? null,
                    'tipo_elaboracion_id' => $mezcalData['tipo_elaboracion_id'] ?? null,
                    'categoria_mezcal_id' => $mezcalData['categoria_mezcal_id'] ?? null,
                    'tipo_maduracion_id' => $mezcalData['tipo_maduracion_id'] ?? null,
                    'maestro_id' => $mezcalData['maestro_id'] ?? null,
                    'palenque_id' => $mezcalData['palenque_id'] ?? null,
                    'nombre' => $mezcalData['nombre'],
                    'slug' => Str::slug($mezcalData['nombre']),
                    'precio_regular' => $mezcalData['precio_regular'] ?? null,
                    'descripcion' => $mezcalData['descripcion'] ?? null,
                    'contenido_alcohol' => $mezcalData['contenido_alcohol'] ?? null,
                    'tamanio_bote' => $mezcalData['tamanio_bote'] ?? null,
                    'proveedor' => $mezcalData['proveedor'] ?? null,
                    'notas_cata' => $mezcalData['notas_cata'] ?? null,
                    'premios' => $mezcalData['premios'] ?? null,
                    'activo' => $mezcalData['activo'] ?? true
                ]);

                // Asociar agaves si se proporcionan
                if (isset($mezcalData['agave_ids']) && is_array($mezcalData['agave_ids'])) {
                    $mezcal->agaves()->attach($mezcalData['agave_ids']);
                }

                $mezcals[] = $mezcal;
            }

            DB::commit();

            $data = [
                'message' => 'Mezcales creados correctamente.',
                'status' => 201,
                'data' => [
                    'mezcals' => $mezcals
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los mezcales.',
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
            $mezcal = Mezcal::with([
                'logo', 
                'gallery', 
                'regions', 
                'marcas', 
                'tipo_maduracion', 
                'categoria_mezcal', 
                'tipo_elaboracion', 
                'maestros', 
                'palenques', 
                'agaves'
            ])
                ->find($id);

            if (!$mezcal) {
                $data = [
                    'message' => 'Mezcal no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Mezcal obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'mezcal' => $mezcal
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el mezcal.',
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
            $mezcal = Mezcal::find($id);

            if (!$mezcal) {
                $data = [
                    'message' => 'Mezcal no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'region_id' => 'nullable|exists:regions,id',
                'marca_id' => 'nullable|exists:marcas,id',
                'tipo_elaboracion_id' => 'nullable|exists:tipo_elaboracion,id',
                'categoria_mezcal_id' => 'nullable|exists:categoria_mezcal,id',
                'tipo_maduracion_id' => 'nullable|exists:tipo_maduracion,id',
                'maestro_id' => 'nullable|exists:maestros,id',
                'palenque_id' => 'nullable|exists:palenques,id',
                'nombre' => 'required|string|max:100',
                'precio_regular' => 'nullable|numeric|min:0',
                'descripcion' => 'nullable|string',
                'contenido_alcohol' => 'nullable|numeric|min:0|max:100',
                'tamanio_bote' => 'nullable|string|max:50',
                'proveedor' => 'nullable|string|max:100',
                'notas_cata' => 'nullable|string',
                'premios' => 'nullable|array',
                'agave_ids' => 'nullable|array',
                'agave_ids.*' => 'exists:agaves,id',
                'activo' => 'boolean'
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

            $mezcal->update([
                'region_id' => $request->region_id,
                'marca_id' => $request->marca_id,
                'tipo_elaboracion_id' => $request->tipo_elaboracion_id,
                'categoria_mezcal_id' => $request->categoria_mezcal_id,
                'tipo_maduracion_id' => $request->tipo_maduracion_id,
                'maestro_id' => $request->maestro_id,
                'palenque_id' => $request->palenque_id,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'precio_regular' => $request->precio_regular,
                'descripcion' => $request->descripcion,
                'contenido_alcohol' => $request->contenido_alcohol,
                'tamanio_bote' => $request->tamanio_bote,
                'proveedor' => $request->proveedor,
                'notas_cata' => $request->notas_cata,
                'premios' => $request->premios,
                'activo' => $request->activo
            ]);

            // Actualizar agaves si se proporcionan
            if ($request->has('agave_ids')) {
                $mezcal->agaves()->sync($request->agave_ids ?? []);
            }

            DB::commit();

            $data = [
                'message' => 'Mezcal actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'mezcal' => $mezcal->load([
                        'regions', 
                        'marcas', 
                        'tipo_maduracion', 
                        'categoria_mezcal', 
                        'tipo_elaboracion', 
                        'maestros', 
                        'palenques', 
                        'agaves'
                    ])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al actualizar el mezcal.',
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
            $mezcal = Mezcal::find($id);

            if (!$mezcal) {
                $data = [
                    'message' => 'Mezcal no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            DB::beginTransaction();

            // Eliminar relaciones con agaves
            $mezcal->agaves()->detach();
            
            $mezcal->delete();

            DB::commit();

            $data = [
                'message' => 'Mezcal eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al eliminar el mezcal.',
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
            $mezcal = Mezcal::find($id);

            if (!$mezcal) {
                $data = [
                    'message' => 'Mezcal no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('region_id')) {
                $rules['region_id'] = 'nullable|exists:regions,id';
            }
            if ($request->has('marca_id')) {
                $rules['marca_id'] = 'nullable|exists:marcas,id';
            }
            if ($request->has('tipo_elaboracion_id')) {
                $rules['tipo_elaboracion_id'] = 'nullable|exists:tipo_elaboracion,id';
            }
            if ($request->has('categoria_mezcal_id')) {
                $rules['categoria_mezcal_id'] = 'nullable|exists:categoria_mezcal,id';
            }
            if ($request->has('tipo_maduracion_id')) {
                $rules['tipo_maduracion_id'] = 'nullable|exists:tipo_maduracion,id';
            }
            if ($request->has('maestro_id')) {
                $rules['maestro_id'] = 'nullable|exists:maestros,id';
            }
            if ($request->has('palenque_id')) {
                $rules['palenque_id'] = 'nullable|exists:palenques,id';
            }
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:100';
            }
            if ($request->has('precio_regular')) {
                $rules['precio_regular'] = 'nullable|numeric|min:0';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('contenido_alcohol')) {
                $rules['contenido_alcohol'] = 'nullable|numeric|min:0|max:100';
            }
            if ($request->has('tamanio_bote')) {
                $rules['tamanio_bote'] = 'nullable|string|max:50';
            }
            if ($request->has('proveedor')) {
                $rules['proveedor'] = 'nullable|string|max:100';
            }
            if ($request->has('notas_cata')) {
                $rules['notas_cata'] = 'nullable|string';
            }
            if ($request->has('premios')) {
                $rules['premios'] = 'nullable|array';
            }
            if ($request->has('agave_ids')) {
                $rules['agave_ids'] = 'nullable|array';
                $rules['agave_ids.*'] = 'exists:agaves,id';
            }
            if ($request->has('activo')) {
                $rules['activo'] = 'boolean';
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

            DB::beginTransaction();

            $updateData = $request->only(array_keys($rules));
            
            if (isset($updateData['nombre'])) {
                $updateData['slug'] = Str::slug($updateData['nombre']);
            }

            // Remover agave_ids del array de actualización
            $agaveIds = null;
            if (isset($updateData['agave_ids'])) {
                $agaveIds = $updateData['agave_ids'];
                unset($updateData['agave_ids']);
            }

            $mezcal->update($updateData);

            // Actualizar agaves si se proporcionan
            if ($agaveIds !== null) {
                $mezcal->agaves()->sync($agaveIds);
            }

            DB::commit();

            $data = [
                'message' => 'Mezcal actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'mezcal' => $mezcal->load([
                        'regions', 
                        'marcas', 
                        'tipo_maduracion', 
                        'categoria_mezcal', 
                        'tipo_elaboracion', 
                        'maestros', 
                        'palenques', 
                        'agaves'
                    ])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al actualizar el mezcal.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
