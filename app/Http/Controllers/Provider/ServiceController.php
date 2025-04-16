<?php
namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ServiceController extends Controller
{
    public function index()
    {
        // Already using Auth::id()
        $services = Service::where('provider_id', Auth::id())->get();
        return view('service_provider.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('service_provider.services.create', compact('categories')); // Corrected path
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'nullable|string|max:255',
            'service_type' => 'required|in:on_site,remote,business_based',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
        ]);

        // Create the service with authenticated user's ID
        $service = Service::create([
            'provider_id' => Auth::id(), // Use authenticated user ID instead of hardcoded value
            'title' => $request->title,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'status' => 'pending',
            'view_count' => 0,
            'service_type' => $request->service_type,
        ]);

        // Handle the image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('service_images', 'public');  // Store the image in the 'service_images' folder

            // Save the image details to the service_images table
            $service->images()->create([
                'image_url' => $imagePath,
                'is_primary' => true,  // Mark this image as the primary image
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }


    public function edit(Service $service)
    {
        // Check if the service belongs to the authenticated user
        if ($service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        return view('service_provider.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        // Check if the service belongs to the authenticated user
        if ($service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'nullable|string|max:255',
            'service_type' => 'required|in:on_site,remote,bussiness_based',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('service_images', 'public');
        
            // If there's an old image, delete it (optional)
            $oldImage = $service->images()->where('is_primary', true)->first();
            if ($oldImage) {
                \Storage::disk('public')->delete($oldImage->image_url);
                $oldImage->delete();
            }
        
            // Save the new image
            $service->images()->create([
                'image_url' => $imagePath,
                'is_primary' => true,
            ]);
        }
        

        $service->update($request->only('title', 'category_id', 'description', 'price', 'location', 'service_type'));

        return redirect()->route('services.index')->with('success', 'Service updated.');

    }

    public function destroy(Service $service)
    {
        // Check if the service belongs to the authenticated user
        if ($service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                // Foreign key violation
                return redirect()->route('services.index')
                    ->with('error', 'This service cannot be deleted because it has existing bookings.');
            }

            // Other database error
            return redirect()->route('services.index')
                ->with('error', 'An unexpected error occurred while trying to delete the service.');
        }
    }


    public function show(Service $service)
    {
        // Check if the service belongs to the authenticated user
        if ($service->provider_id != Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $service->load('category', 'images'); // eager load relationships
        return view('service_provider.services.show', compact('service'));
    }
}
