<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class VehicleService
{
    private array $allowedSorts = [
        'id', 'placa', 'chassi', 'marca', 'modelo', 'versao', 'valor_venda', 'cor', 'km',
        'cambio', 'combustivel', 'created_at'
    ];
    private array $allowedFilters = ['placa', 'marca', 'modelo'];

    /**
     * Lista veículos com filtros e ordenação
     * @throws Throwable
     */
    public function list(Request $request): LengthAwarePaginator
    {
        $query = Vehicle::query()->with(['images', 'owner:id,name,email', 'updater:id,name,email']);

        if ($request->filled('q')) {
            $this->applyGlobalSearch($query, $request->input('q'));
        }

        $this->applyFilters($query, $request);

        $request->whenFilled('sort', function (string $sort) use ($query) {
            $this->applySorting($query, $sort);
        }, function () use ($query) {
            $query->latest();
        });

        $perPage = $request->integer('per_page', config('pagination.per_page.default'));
        $perPage = max(1, min($perPage, 100));

        $page = $request->input('page', 1);

        return $query->paginate(
            perPage: $perPage,
            page: $page,
        );
    }

    /**
     * Remove um veículo e as suas imagens
     * @throws Throwable
     */
    public function delete(Vehicle $vehicle): void
    {
        $vehicle = $vehicle->loadMissing('images');
        $disk = config('vehicles.images.disk');
        $directory = config('vehicles.images.directory');

        DB::transaction(function () use ($vehicle, $disk, $directory) {
            Storage::disk($disk)->deleteDirectory($directory.'/'.$vehicle->id);
            $vehicle->images()->delete();
            $vehicle->delete();
        });
    }


    private function applyGlobalSearch($query, string $searchTerm): void
    {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('placa', 'LIKE', "%{$searchTerm}%")
                ->orWhere('marca', 'LIKE', "%{$searchTerm}%")
                ->orWhere('modelo', 'LIKE', "%{$searchTerm}%");
        });
    }


    /**
     * Aplica filtros à query
     */
    private function applyFilters($query, Request $request): void
    {
        foreach ($this->allowedFilters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'LIKE', '%'.$request->input($filter).'%');
            }
        }
    }

    /**
     * Aplica ordenação à query
     */
    private function applySorting($query, string $sort): void
    {
        $sorts = explode(',', $sort);

        foreach ($sorts as $sort) {
            $sort = trim($sort);

            if (empty($sort)) {
                continue;
            }

            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $field = ltrim($sort, '-');

            $field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);

            if (in_array($field, $this->allowedSorts)) {
                $query->orderBy($field, $direction);
            }
        }
    }

}
