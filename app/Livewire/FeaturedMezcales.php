<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Mezcal;
use App\Models\Region;
use App\Models\CategoriaMezcal;
use App\Models\TipoMaduracion;
use App\Models\TipoElaboracion;
use App\Models\Agave;
use Livewire\WithPagination;

class FeaturedMezcales extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para los filtros
    public $selectedTiposMaduracion = [];
    public $selectedTiposElaboracion = [];
    public $selectedRegiones = [];
    public $selectedCategorias = [];
    public $selectedTiposAgave = [];
    
    // Filtro de precio
    public $precioMin = 0;
    public $precioMax = 10000;

    // Para resetear la paginación cuando cambien los filtros
    public function updatedSelectedTiposMaduracion()
    {
        $this->resetPage();
    }

    public function updatedSelectedTiposElaboracion()
    {
        $this->resetPage();
    }

    public function updatedSelectedRegiones()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategorias()
    {
        $this->resetPage();
    }

    public function updatedSelectedTiposAgave()
    {
        $this->resetPage();
    }

    public function updatedPrecioMin()
    {
        $this->resetPage();
    }

    public function updatedPrecioMax()
    {
        $this->resetPage();
    }

    // Método para remover filtro individual
    public function removeFilter($type, $value)
    {
        switch ($type) {
            case 'tipo_maduracion':
                $this->selectedTiposMaduracion = array_filter($this->selectedTiposMaduracion, fn($id) => $id != $value);
                break;
            case 'tipo_elaboracion':
                $this->selectedTiposElaboracion = array_filter($this->selectedTiposElaboracion, fn($id) => $id != $value);
                break;
            case 'region':
                $this->selectedRegiones = array_filter($this->selectedRegiones, fn($id) => $id != $value);
                break;
            case 'categoria':
                $this->selectedCategorias = array_filter($this->selectedCategorias, fn($id) => $id != $value);
                break;
            case 'tipo_agave':
                $this->selectedTiposAgave = array_filter($this->selectedTiposAgave, fn($id) => $id != $value);
                break;
        }
        $this->resetPage();
    }

    // Método para limpiar todos los filtros
    public function clearAllFilters()
    {
        $this->selectedTiposMaduracion = [];
        $this->selectedTiposElaboracion = [];
        $this->selectedRegiones = [];
        $this->selectedCategorias = [];
        $this->selectedTiposAgave = [];
        $this->precioMin = 0;
        $this->precioMax = 10000;
        $this->resetPage();
    }

    public function render()
    {
        // Consulta base de mezcales con filtros aplicados
        $mezcalesQuery = Mezcal::query()
            ->with(['images' => function ($query) {
                $query->orderByDesc('is_featured')->orderBy('order');
            }])
            ->with('region:id,nombre')
            ->with('tipo_maduracion:id,nombre')
            ->with('tipo_elaboracion:id,nombre')
            ->with('categoria_mezcal:id,nombre')
            ->select([
                'id', 'region_id', 'marca_id', 'tipo_maduracion_id', 
                'categoria_mezcal_id', 'tipo_elaboracion_id', 'maestro_id', 
                'palenque_id', 'nombre', 'slug', 'precio_regular', 
                'descripcion', 'contenido_alcohol', 'tamanio_bote', 
                'proveedor', 'notas_cata', 'premios', 'activo'
            ])
            ->where('activo', true);

        // Aplicar filtros
        if (!empty($this->selectedTiposMaduracion)) {
            $mezcalesQuery->whereIn('tipo_maduracion_id', $this->selectedTiposMaduracion);
        }

        if (!empty($this->selectedTiposElaboracion)) {
            $mezcalesQuery->whereIn('tipo_elaboracion_id', $this->selectedTiposElaboracion);
        }

        if (!empty($this->selectedRegiones)) {
            $mezcalesQuery->whereIn('region_id', $this->selectedRegiones);
        }

        if (!empty($this->selectedCategorias)) {
            $mezcalesQuery->whereIn('categoria_mezcal_id', $this->selectedCategorias);
        }

        // Para tipos de agave con tabla pivot agaves_mezcals
        if (!empty($this->selectedTiposAgave)) {
            $mezcalesQuery->whereHas('agaves', function ($query) {
                $query->whereIn('agaves.id', $this->selectedTiposAgave);
            });
        }

        // Filtro de precio
        if ($this->precioMin > 0 || $this->precioMax < 10000) {
            $mezcalesQuery->whereBetween('precio_regular', [$this->precioMin, $this->precioMax]);
        }

        $mezcales = $mezcalesQuery->latest('id')->paginate(5);

        // Obtener datos para los filtros (ordenados por 'orden' y luego 'nombre')
        $tiposMaduracion = TipoMaduracion::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();
        $tiposElaboracion = TipoElaboracion::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();
        $regiones = Region::orderBy('nombre')->get(); // Region no tiene columna 'activo'
        $categorias = CategoriaMezcal::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();
        $tiposAgave = Agave::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();

        // Obtener nombres para mostrar en los filtros aplicados
        $filtrosAplicados = $this->getFiltrosAplicados();

        return view('livewire.featured-mezcales', [
            'mezcales' => $mezcales,
            'tiposMaduracion' => $tiposMaduracion,
            'tiposElaboracion' => $tiposElaboracion,
            'regiones' => $regiones,
            'categorias' => $categorias,
            'tiposAgave' => $tiposAgave,
            'filtrosAplicados' => $filtrosAplicados,
        ]);
    }

    private function getFiltrosAplicados()
    {
        $filtros = [];

        // Tipos de maduración
        if (!empty($this->selectedTiposMaduracion)) {
            $nombres = TipoMaduracion::whereIn('id', $this->selectedTiposMaduracion)->pluck('nombre', 'id');
            foreach ($nombres as $id => $nombre) {
                $filtros[] = [
                    'type' => 'tipo_maduracion',
                    'id' => $id,
                    'nombre' => $nombre
                ];
            }
        }

        // Tipos de elaboración
        if (!empty($this->selectedTiposElaboracion)) {
            $nombres = TipoElaboracion::whereIn('id', $this->selectedTiposElaboracion)->pluck('nombre', 'id');
            foreach ($nombres as $id => $nombre) {
                $filtros[] = [
                    'type' => 'tipo_elaboracion',
                    'id' => $id,
                    'nombre' => $nombre
                ];
            }
        }

        // Regiones
        if (!empty($this->selectedRegiones)) {
            $nombres = Region::whereIn('id', $this->selectedRegiones)->pluck('nombre', 'id');
            foreach ($nombres as $id => $nombre) {
                $filtros[] = [
                    'type' => 'region',
                    'id' => $id,
                    'nombre' => $nombre
                ];
            }
        }

        // Categorías
        if (!empty($this->selectedCategorias)) {
            $nombres = CategoriaMezcal::whereIn('id', $this->selectedCategorias)->pluck('nombre', 'id');
            foreach ($nombres as $id => $nombre) {
                $filtros[] = [
                    'type' => 'categoria',
                    'id' => $id,
                    'nombre' => $nombre
                ];
            }
        }

        // Tipos de agave
        if (!empty($this->selectedTiposAgave)) {
            $nombres = Agave::whereIn('id', $this->selectedTiposAgave)->pluck('nombre', 'id');
            foreach ($nombres as $id => $nombre) {
                $filtros[] = [
                    'type' => 'tipo_agave',
                    'id' => $id,
                    'nombre' => $nombre
                ];
            }
        }

        return $filtros;
    }
}