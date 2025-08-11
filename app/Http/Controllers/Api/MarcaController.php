<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $marcas = Marca::with(['logo', 'gallery', 'regions', 'mezcals'])
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            if ($marcas->isEmpty()) {
                $data = [
                    'message' => 'No hay marcas registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de marcas obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'marcas' => $marcas
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las marcas.',
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
            // Detectar si es un array de marcas o una sola marca
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear la(s) marca(s).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single marca
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|exists:regions,id',
            'nombre' => 'required|string|max:40',
            'certificado_dom' => 'nullable|string|max:100',
            'descripcion' => 'required|string',
            'historia' => 'nullable|string',
            'eslogan' => 'nullable|string|max:100',
            'anio_fundacion' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'telefono' => 'nullable|digits_between:7,15',
            'correo' => 'nullable|email',
            'redes_sociales' => 'nullable|string|max:255',
            'sitio_web' => 'nullable|url|max:255',
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

        $marca = Marca::create([
            'region_id' => $request->region_id,
            'nombre' => $request->nombre,
            'slug' => $slug,
            'certificado_dom' => $request->certificado_dom,
            'descripcion' => $request->descripcion,
            'historia' => $request->historia,
            'eslogan' => $request->eslogan,
            'anio_fundacion' => $request->anio_fundacion,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'redes_sociales' => $request->redes_sociales,
            'sitio_web' => $request->sitio_web,
            'activo' => $request->activo ?? true
        ]);

        $data = [
            'message' => 'Marca creada correctamente.',
            'status' => 201,
            'data' => [
                'marca' => $marca->load(['regions'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple marcas
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.region_id' => 'nullable|exists:regions,id',
            '*.nombre' => 'required|string|max:40',
            '*.certificado_dom' => 'nullable|string|max:100',
            '*.descripcion' => 'required|string',
            '*.historia' => 'nullable|string',
            '*.eslogan' => 'nullable|string|max:100',
            '*.anio_fundacion' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            '*.telefono' => 'nullable|digits_between:7,15',
            '*.correo' => 'nullable|email',
            '*.redes_sociales' => 'nullable|string|max:255',
            '*.sitio_web' => 'nullable|url|max:255',
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
            $marcas = [];
            foreach ($request->all() as $marcaData) {
                $marca = Marca::create([
                    'region_id' => $marcaData['region_id'] ?? null,
                    'nombre' => $marcaData['nombre'],
                    'slug' => Str::slug($marcaData['nombre']),
                    'certificado_dom' => $marcaData['certificado_dom'] ?? null,
                    'descripcion' => $marcaData['descripcion'],
                    'historia' => $marcaData['historia'] ?? null,
                    'eslogan' => $marcaData['eslogan'] ?? null,
                    'anio_fundacion' => $marcaData['anio_fundacion'] ?? null,
                    'telefono' => $marcaData['telefono'] ?? null,
                    'correo' => $marcaData['correo'] ?? null,
                    'redes_sociales' => $marcaData['redes_sociales'] ?? null,
                    'sitio_web' => $marcaData['sitio_web'] ?? null,
                    'activo' => $marcaData['activo'] ?? true
                ]);

                $marcas[] = $marca;
            }

            DB::commit();

            $data = [
                'message' => 'Marcas creadas correctamente.',
                'status' => 201,
                'data' => [
                    'marcas' => $marcas
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las marcas.',
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
            $marca = Marca::with(['logo', 'gallery', 'regions', 'mezcals'])
                ->find($id);

            if (!$marca) {
                $data = [
                    'message' => 'Marca no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Marca obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'marca' => $marca
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la marca.',
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
            $marca = Marca::find($id);

            if (!$marca) {
                $data = [
                    'message' => 'Marca no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'region_id' => 'nullable|exists:regions,id',
                'nombre' => 'required|string|max:40',
                'certificado_dom' => 'nullable|string|max:100',
                'descripcion' => 'required|string',
                'historia' => 'nullable|string',
                'eslogan' => 'nullable|string|max:100',
                'anio_fundacion' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
                'telefono' => 'nullable|digits_between:7,15',
                'correo' => 'nullable|email',
                'redes_sociales' => 'nullable|string|max:255',
                'sitio_web' => 'nullable|url|max:255',
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

            $marca->update([
                'region_id' => $request->region_id,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'certificado_dom' => $request->certificado_dom,
                'descripcion' => $request->descripcion,
                'historia' => $request->historia,
                'eslogan' => $request->eslogan,
                'anio_fundacion' => $request->anio_fundacion,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'redes_sociales' => $request->redes_sociales,
                'sitio_web' => $request->sitio_web,
                'activo' => $request->activo
            ]);

            $data = [
                'message' => 'Marca actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'marca' => $marca->load(['regions'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la marca.',
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
            $marca = Marca::withCount(['mezcals'])->find($id);

            if (!$marca) {
                $data = [
                    'message' => 'Marca no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($marca->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar la marca porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $marca->delete();

            $data = [
                'message' => 'Marca eliminada correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la marca.',
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
            $marca = Marca::find($id);

            if (!$marca) {
                $data = [
                    'message' => 'Marca no encontrada.',
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
                $rules['nombre'] = 'string|max:40';
            }
            if ($request->has('certificado_dom')) {
                $rules['certificado_dom'] = 'nullable|string|max:100';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'string';
            }
            if ($request->has('historia')) {
                $rules['historia'] = 'nullable|string';
            }
            if ($request->has('eslogan')) {
                $rules['eslogan'] = 'nullable|string|max:100';
            }
            if ($request->has('anio_fundacion')) {
                $rules['anio_fundacion'] = 'nullable|integer|min:1800|max:' . (date('Y') + 1);
            }
            if ($request->has('telefono')) {
                $rules['telefono'] = 'nullable|digits_between:7,15';
            }
            if ($request->has('correo')) {
                $rules['correo'] = 'nullable|email';
            }
            if ($request->has('redes_sociales')) {
                $rules['redes_sociales'] = 'nullable|string|max:255';
            }
            if ($request->has('sitio_web')) {
                $rules['sitio_web'] = 'nullable|url|max:255';
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

            $updateData = $request->only(array_keys($rules));
            
            if (isset($updateData['nombre'])) {
                $updateData['slug'] = Str::slug($updateData['nombre']);
            }

            $marca->update($updateData);

            $data = [
                'message' => 'Marca actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'marca' => $marca->load(['regions'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la marca.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
