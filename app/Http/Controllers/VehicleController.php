<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Services\VehicleService;
use App\Http\Requests\VehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Throwable;

class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleService $vehicleService
    ) {
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Vehicle::class);

        return VehicleResource::collection($this->vehicleService->list($request));
    }

    public function store(VehicleRequest $request): JsonResponse
    {
        Gate::authorize('create', Vehicle::class);

        $user = $request->user();

        return (new VehicleResource($user->vehicles()->create($request->validated())))
            ->additional([
                'message' => 'Veículo criado com sucesso.'
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id)
    {
        Gate::authorize('view', Vehicle::class);

        return new VehicleResource(
            Vehicle::with(['images', 'owner:id,name,email', 'updater:id,name,email'])->findOrFail($id)
        );
    }

    public function update(VehicleRequest $request, int $id): VehicleResource
    {
        $vehicle = Vehicle::findOrFail($id);

        Gate::authorize('update', $vehicle);

        $vehicle->update($request->validated());

        return (new VehicleResource($vehicle))
            ->additional([
                'message' => 'Veículo atualizado com sucesso.',
            ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($id);

        Gate::authorize('delete', $vehicle);

        $this->vehicleService->delete($vehicle);

        return response()->json(['message' => 'Veículo apagado com sucesso.']);
    }
}
