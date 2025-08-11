<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $regions = Region::all();

            if ($regions->isEmpty()) {
                $data = [
                    'message' => 'No hay regiones registradas.',
                    'status' => 404,
                ];
                return response()->json($data, 404);
            }

            $data = [
                'regions' => $regions,
                'status' => 200,
                'message' => 'Lista de regiones obtenida correctamente.',
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las regiones.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Supports both single region and multiple regions creation.
     */
    public function store(Request $request)
    {
        try {
            // Detectar si es un array de regiones o una sola región
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
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single region
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:25',
            'codigo' => 'required|string|max:10|unique:regions,codigo',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => $validator->errors(),
                'status' => 422
            ];
            return response()->json($data, 422);
        }

        $region = Region::create([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
        ]);

        $data = [
            'region' => $region,
            'status' => 201,
            'message' => 'Región creada correctamente.'
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple regions
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.nombre' => 'required|string|max:25',
            '*.codigo' => 'required|string|max:10|distinct',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => $validator->errors(),
                'status' => 422
            ];
            return response()->json($data, 422);
        }

        // Verificar que los códigos no existan en la base de datos
        $codigos = collect($request->all())->pluck('codigo');
        $existingCodigos = Region::whereIn('codigo', $codigos)->pluck('codigo');

        if ($existingCodigos->isNotEmpty()) {
            $data = [
                'message' => 'Los siguientes códigos ya existen: ' . $existingCodigos->implode(', '),
                'status' => 422
            ];
            return response()->json($data, 422);
        }

        DB::beginTransaction();
        
        try {
            $regions = [];
            foreach ($request->all() as $regionData) {
                $regions[] = Region::create([
                    'nombre' => $regionData['nombre'],
                    'codigo' => $regionData['codigo'],
                ]);
            }

            DB::commit();

            $data = [
                'regions' => $regions,
                'status' => 201,
                'message' => count($regions) . ' regiones creadas correctamente.'
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las regiones.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        try {
            $region = Region::find($id);

            if (!$region) {
                $data = [
                    'message' => 'Región no encontrada.',
                    'status' => 404,
                ];
                return response()->json($data, 404);
            }

            $data = [
                'region' => $region,
                'status' => 200,
                'message' => 'Región obtenida correctamente.'
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la región.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        try {
            $region = Region::find($id);

            if (!$region) {
                $data = [
                    'message' => 'Región no encontrada.',
                    'status' => 404,
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:25',
                'codigo' => 'required|string|max:10|unique:regions,codigo,' . $id,
            ]);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422
                ];
                return response()->json($data, 422);
            }

            $region->nombre = $request->nombre;
            $region->codigo = $request->codigo;
            $region->save();

            $data = [
                'region' => $region,
                'status' => 200,
                'message' => 'Región actualizada correctamente.'
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la región.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        try {
            $region = Region::find($id);

            if (!$region) {
                $data = [
                    'message' => 'Región no encontrada.',
                    'status' => 404,
                ];
                return response()->json($data, 404);
            }

            $region->delete();

            $data = [
                'message' => 'Región eliminada correctamente.',
                'status' => 200,
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la región.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update partially the specified resource in storage.
     */
    public function updatePartial(Request $request, string $id)
    {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
            ];
            return response()->json($data, 400);
        }

        try {
            $region = Region::find($id);

            if (!$region) {
                $data = [
                    'message' => 'Región no encontrada.',
                    'status' => 404,
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:25';
            }
            if ($request->has('codigo')) {
                $rules['codigo'] = 'string|max:10|unique:regions,codigo,' . $id;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422
                ];
                return response()->json($data, 422);
            }

            if ($request->has('nombre')) {
                $region->nombre = $request->nombre;
            }

            if ($request->has('codigo')) {
                $region->codigo = $request->codigo;
            }

            $region->save();

            $data = [
                'region' => $region,
                'status' => 200,
                'message' => 'Región actualizada correctamente.'
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la región.',
                'status' => 500,
            ];
            return response()->json($data, 500);
        }
    }
}