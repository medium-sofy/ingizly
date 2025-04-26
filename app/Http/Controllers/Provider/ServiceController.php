<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('provider_id', Auth::id())->with('images')->get();
        return view('service_provider.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('service_provider.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'nullable|string|max:255',
            'service_type' => 'required|in:on_site,remote,bussiness_based',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $service = Service::create([
            'provider_id' => Auth::id(),
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'status' => 'pending',
            'view_count' => 0,
            'service_type' => $validated['service_type'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('service_images', 'public');
                $service->images()->create([
                    'image_url' => $imagePath,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('provider.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $this->authorizeService($service);

        $categories = Category::all();
        $service->load('images');

        return view('service_provider.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'nullable|string|max:255',
            'service_type' => 'required|in:on_site,remote,bussiness_based',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:service_images,id'
        ]);

        // Delete selected images
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ServiceImage::find($imageId);
                if ($image && $image->service_id == $service->id) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }
            }
        }

        // Upload new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('service_images', 'public');
                $service->images()->create([
                    'image_url' => $imagePath,
                    'is_primary' => false,
                ]);
            }
        }

        $service->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'service_type' => $validated['service_type'],
        ]);

        return redirect()->route('provider.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $this->authorizeService($service);

        try {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }

            $service->delete();
            return redirect()->route('provider.services.index')->with('success', 'Service deleted successfully.');
        } catch (QueryException $e) {
            return redirect()->route('provider.services.index')->with(
                'error',
                $e->getCode() === '23000'
                    ? 'This service cannot be deleted because it has existing bookings.'
                    : 'An error occurred while deleting the service.'
            );
        }
    }

    public function show(Service $service)
    {
        $this->authorizeService($service);

        $service->load('category', 'images');
        return view('service_provider.services.show', compact('service'));
    }

    public function destroyImage($id)
    {
        $image = ServiceImage::findOrFail($id);

        if ($image->service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($image->image_url);
        $image->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    private function authorizeService(Service $service)
    {
        if ($service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}