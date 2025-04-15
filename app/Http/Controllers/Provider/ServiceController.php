<?php
namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('provider_id', 1)->with('category')->get(); // 👈 hardcoded for testing
        return view('service_provider.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('service_provider.services.create', compact('categories')); // Corrected path
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'nullable|string|max:255',
            'service_type' => 'required|in:on_site,remote,bussiness_based', // Validate service_type
        ]);

        Service::create([
            'provider_id' => 1, // Use a default provider ID for testing
            'title' => $request->title,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'location' => $request->location,
            'status' => 'pending',
            'view_count' => 0,
            'service_type' => $request->service_type, // Use the validated service_type
        ]);

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
{
    if ($service->provider_id != 1) {
        abort(403, 'Unauthorized action.');
    }

    $categories = Category::all();
    return view('service_provider.services.edit', compact('service', 'categories'));
}

public function update(Request $request, Service $service)
{
    if ($service->provider_id != 1) {
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

    $service->update($request->only('title', 'category_id', 'description', 'price', 'location', 'service_type'));

    return redirect()->route('services.index')->with('success', 'Service updated.');

}

public function destroy(Service $service)
{
    if ($service->provider_id != 1) {
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
        // show service details here
        return view('service_provider.services.show', compact('service'));
    }
}
