<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\ServiceRequest;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('throttle:60,1');
    }

    public function index(): JsonResponse
    {
        $services = Cache::remember('user_services_' . auth()->id(), 3600, function () {
            return auth()->user()->services()->with('user')->latest()->paginate(10);
        });

        return response()->json([
            'status' => 'success',
            'data' => ServiceResource::collection($services),
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ]
        ]);
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        try {
            $service = auth()->user()->services()->create($request->validated());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Service created successfully',
                'data' => new ServiceResource($service)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Service $service): JsonResponse
    {
        $this->authorize('view', $service);
        
        return response()->json([
            'status' => 'success',
            'data' => new ServiceResource($service->load('user'))
        ]);
    }

    public function update(ServiceRequest $request, Service $service): JsonResponse
    {
        $this->authorize('update', $service);

        try {
            $service->update($request->validated());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Service updated successfully',
                'data' => new ServiceResource($service)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->authorize('delete', $service);

        try {
            $service->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}