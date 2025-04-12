<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Base query with relationships
        $query = Service::with(['serviceProvider.user', 'category']);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('serviceProvider.user', function($provider) use ($search) {
                        $provider->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Apply price range filter
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply category filter
        if ($request->has('category') && $request->category != 'All Categories') {
            $query->where('category_id', $request->category);
        }

        // Apply location filter
        if ($request->has('location') && $request->location != 'All Locations') {
            $query->whereHas('serviceProvider', function($provider) use ($request) {
                $provider->where('location', $request->location);
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status != 'All Statuses') {
            $query->where('status', strtolower($request->status));
        }

        // Get categories for filter dropdown
        $categories = Category::all();

        // Get unique locations for filter dropdown
        $locations = ServiceProvider::select('location')->distinct()->get();

        // Get paginated results
        $services = $query->latest()->paginate(10);

        // Append query parameters to pagination links
        $services->appends($request->all());

        return view('admin.services.index', compact('services', 'categories', 'locations'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $providers = ServiceProvider::with('user')->get();

        return view('admin.services.create', compact('categories', 'providers'));
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'provider_id' => 'required|exists:service_providers,user_id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,pending,inactive',
            'service_type' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $serviceData = [
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'provider_id' => $request->provider_id,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'service_type' => $request->service_type,
            'location' => $request->location,
            'view_count' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/services', $imageName);
            $serviceData['image'] = 'services/' . $imageName;
        }

        Service::create($serviceData);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }
    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $service = Service::with(['serviceProvider.user', 'category'])->findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $categories = Category::all();
        $providers = ServiceProvider::with('user')->get();

        return view('admin.services.edit', compact('service', 'categories', 'providers'));
    }

    /**
     * Update the specified service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'provider_id' => 'required|exists:service_providers,user_id',
            'status' => 'required|in:active,pending,inactive'
        ]);

        $service = Service::findOrFail($id);

        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'provider_id' => $request->provider_id,
            'status' => $request->status
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Approve a pending service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $service = Service::findOrFail($id);
        $service->status = 'active';
        $service->save();


        return redirect()->back()->with('success', 'Service has been approved');
    }

    /**
     * Reject a pending service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $service = Service::findOrFail($id);
        $service->status = 'inactive';
        $service->save();

        return redirect()->back()->with('success', 'Service has been rejected');
    }
}
