<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\ServiceProvider;
use App\Models\ServiceImage; // <-- Add ServiceImage model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     * (Code remains largely the same - ensure relationships are eager-loaded)
     */
    public function index(Request $request)
    {
        // Eager load necessary relationships for display and filtering
        $query = Service::with(['provider.user', 'category', 'images']); // Eager load images

        // --- Filtering logic remains the same ---
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('provider.user', function($providerUser) use ($search) {
                        $providerUser->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        // Price filter
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }
        // Category filter
        if ($request->has('category') && is_numeric($request->category)) { // Ensure category ID is numeric
            $query->where('category_id', $request->category);
        }
        // Location filter (uses ServiceProvider location)
        if ($request->has('location') && !empty($request->location) && $request->location !== 'All Locations') {
            $query->whereHas('provider', function($provider) use ($request) {
                $provider->where('location', $request->location);
            });
        }
        // Status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'All Statuses') {
            $query->where('status', strtolower($request->status));
        }
        // --- End filtering logic ---

        // Get categories for filter dropdown
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        // Get unique locations for filter dropdown (from Service Providers)
        $locations = ServiceProvider::select('location')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location'); // Use pluck for simpler array

        $services = $query->latest()->paginate(10);
        $services->appends($request->all());

        return view('admin.services.index', compact('services', 'categories', 'locations'));
    }


    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        // Get providers identified by their user name for the dropdown
        $providers = ServiceProvider::with('user')->get()->mapWithKeys(function ($provider) {
            return [$provider->user_id => $provider->user->name . ($provider->business_name ? " ({$provider->business_name})" : '')];
        });
        $serviceTypes = ['on_site', 'shop_based', 'remote']; // Define service types

        return view('admin.services.create', compact('categories', 'providers', 'serviceTypes'));
    }

    /**
     * Store a newly created service and its images in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'provider_id' => 'required|exists:service_providers,user_id', // Check against user_id in service_providers
            'category_id' => 'required|exists:categories,id',
            'status' => ['required', Rule::in(['active', 'pending', 'inactive'])],
            'service_type' => ['required', Rule::in(['on_site', 'shop_based', 'remote'])],
            'location' => 'nullable|string|max:255', // Location specific to the service if different from provider

            // --- Image Validation (allow multiple) ---
            'images'   => 'nullable|array', // Expect an array of images
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate each image in the array
            'primary_image_index' => 'nullable|integer' // Index of the primary image if multiple are uploaded
        ]);

        DB::beginTransaction();
        try {
            // Create Service
            $service = Service::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'provider_id' => $validated['provider_id'],
                'category_id' => $validated['category_id'],
                'status' => $validated['status'],
                'service_type' => $validated['service_type'],
                'location' => $validated['location'], // Service specific location
                'view_count' => 0,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $primaryIndex = $request->input('primary_image_index', 0); // Default to first image
                foreach ($request->file('images') as $index => $image) {
                    $imageName = $service->id . '_' . time() . '_' . $image->getClientOriginalName();
                    $path = $image->storeAs('service_images', $imageName, 'public'); // Store in 'public/service_images'

                    ServiceImage::create([
                        'service_id' => $service->id,
                        'image_url' => $path, // Store the path relative to the storage disk
                        'is_primary' => ($index == $primaryIndex),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create service. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id)
    {
        $service = Service::with('images')->findOrFail($id); // Eager load images
        $categories = Category::orderBy('name')->get();
        $providers = ServiceProvider::with('user')->get()->mapWithKeys(function ($provider) {
            return [$provider->user_id => $provider->user->name . ($provider->business_name ? " ({$provider->business_name})" : '')];
        });
        $serviceTypes = ['on_site', 'shop_based', 'remote'];

        return view('admin.services.edit', compact('service', 'categories', 'providers', 'serviceTypes'));
    }

    /**
     * Update the specified service and its images in storage.
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'provider_id' => 'required|exists:service_providers,user_id',
            'category_id' => 'required|exists:categories,id',
            'status' => ['required', Rule::in(['active', 'pending', 'inactive'])],
            'service_type' => ['required', Rule::in(['on_site', 'shop_based', 'remote'])],
            'location' => 'nullable|string|max:255',

            // --- Image Handling ---
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image_id' => 'nullable|integer|exists:service_images,id', // ID of existing/new primary image
            'deleted_images' => 'nullable|array', // Array of image IDs to delete
            'deleted_images.*' => 'integer|exists:service_images,id',
        ]);

        DB::beginTransaction();
        try {
            // Update Service Details
            $service->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'provider_id' => $validated['provider_id'],
                'category_id' => $validated['category_id'],
                'status' => $validated['status'],
                'service_type' => $validated['service_type'],
                'location' => $validated['location'],
            ]);

            // --- Handle Image Deletions ---
            if ($request->has('deleted_images')) {
                $imagesToDelete = ServiceImage::where('service_id', $service->id)
                    ->whereIn('id', $validated['deleted_images'])
                    ->get();
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->image_url);
                    $img->delete();
                }
            }

            // --- Handle New Image Uploads ---
            $newPrimaryImageId = $request->input('primary_image_id'); // Could be an existing ID or null if new image is primary
            $newImageIsPrimary = false;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = $service->id . '_' . time() . '_' . $image->getClientOriginalName();
                    $path = $image->storeAs('service_images', $imageName, 'public');

                    // Check if this new image should be primary
                    // You'll need logic in your form to indicate if a *new* upload is primary
                    // For simplicity, let's assume only existing images can be primary for now,
                    // unless you add specific form input for "make this new upload primary".
                    $isPrimary = false; // Modify this based on form input if needed

                    $newImage = ServiceImage::create([
                        'service_id' => $service->id,
                        'image_url' => $path,
                        'is_primary' => $isPrimary,
                    ]);
                    // If this newly uploaded image IS designated as primary:
                    // if ($isPrimary) { $newPrimaryImageId = $newImage->id; }
                }
            }

            // --- Set Primary Image ---
            // Reset all images for this service to not primary
            ServiceImage::where('service_id', $service->id)->update(['is_primary' => false]);
            // Set the selected one as primary (if an ID was provided)
            if ($newPrimaryImageId) {
                ServiceImage::where('id', $newPrimaryImageId)
                    ->where('service_id', $service->id) // Ensure it belongs to this service
                    ->update(['is_primary' => true]);
            } elseif (ServiceImage::where('service_id', $service->id)->count() > 0) {
                // If no primary was selected and images exist, make the first one primary
                ServiceImage::where('service_id', $service->id)->orderBy('id', 'asc')->first()->update(['is_primary' => true]);
            }


            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update service. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service and its images from storage.
     */
    public function destroy($id)
    {
        // Note: ON DELETE CASCADE on service_images table handles image record deletion.
        // We need to manually delete the image *files*.
        $service = Service::with('images')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete associated image files
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image->image_url);
            }

            // Deleting the service record will trigger cascade delete for ServiceImage records
            // due to the foreign key constraint in the schema.
            $service->delete();

            DB::commit();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->route('admin.services.index')
                ->with('error', 'Failed to delete service. Error: ' . $e->getMessage());
        }
    }

    // --- approve() and reject() methods remain the same ---
    public function approve($id)
    {
        $service = Service::findOrFail($id);
        if ($service->status == 'pending') { // Only approve if pending
            $service->status = 'active';
            $service->save();
            // TODO: Optionally send notification to provider
            return redirect()->back()->with('success', 'Service has been approved.');
        }
        return redirect()->back()->with('warning', 'Service is not pending approval.');
    }

    public function reject($id)
    {
        $service = Service::findOrFail($id);
        if ($service->status == 'pending') { // Only reject if pending
            $service->status = 'inactive'; // Or maybe a 'rejected' status if you add one
            $service->save();
            // TODO: Optionally send notification to provider with reason
            return redirect()->back()->with('success', 'Service has been rejected.');
        }
        return redirect()->back()->with('warning', 'Service is not pending rejection.');
    }

    // --- show() method remains the same, eager load relationships ---
    public function show($id)
    {
        $service = Service::with(['provider.user', 'category', 'images'])->findOrFail($id);
        // Potentially load reviews, orders etc. if needed for the show view
        // $service->load(['reviews.buyer', 'orders']);
        return view('admin.services.show', compact('service'));
    }
}
