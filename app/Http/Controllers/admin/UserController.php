<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;         // <-- Add Admin model
use App\Models\ServiceBuyer;  // <-- Add ServiceBuyer model
use App\Models\ServiceProvider; // <-- Add ServiceProvider model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // <-- For conditional validation

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     * (Code remains largely the same - ensure relationships are eager-loaded if needed)
     */
    public function index(Request $request)
    {
        // Base query with relationships potentially needed for display
        $query = User::with(['serviceProvider', 'serviceBuyer']); // Eager load role details if needed

        // --- Filtering logic remains the same ---
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                // Add username/phone if columns exist and needed
                // ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }
        // Date filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        // Role filter
        if ($request->has('role') && !empty($request->role) && $request->role !== 'All Roles') {
            $query->where('role', $request->role);
        }
        // --- End filtering logic ---

        $users = $query->latest()->paginate(10);
        $users->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Pass necessary data for role-specific fields if needed
        // e.g., $providerTypes = ['handyman', 'shop_owner'];
        return view('admin.users.create'/*, compact('providerTypes')*/);
    }

    /**
     * Store a newly created user and their role-specific data in storage.
     */
    public function store(Request $request)
    {
        // --- Enhanced Validation ---
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'service_buyer', 'service_provider'])],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjusted field name

            // --- Service Buyer Fields (Conditional) ---
            'buyer_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role == 'service_buyer')],
            'buyer_phone' => ['nullable', 'string', 'max:20', Rule::requiredIf($request->role == 'service_buyer')],

            // --- Service Provider Fields (Conditional) ---
            'provider_phone' => ['nullable', 'string', 'max:20', Rule::requiredIf($request->role == 'service_provider')],
            'provider_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role == 'service_provider')],
            'provider_type' => ['nullable', Rule::in(['handyman', 'bussiness_owner']), Rule::requiredIf($request->role == 'service_provider')],
            'bio' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            // Add is_verified if you allow setting it on creation
        ]);

        DB::beginTransaction();
        try {
            // --- Create User ---
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'email_verified_at' => now(), // Assuming admin-created users are pre-verified
                'is_email_verified' => true,  // Assuming admin-created users are pre-verified
                'last_login' => null,
                'profile_image' => null, // Handle photo upload below
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile_photos', 'public');
                $userData['profile_image'] = $path;
            }

            $user = User::create($userData);

            // --- Create Role-Specific Record ---
            switch ($validated['role']) {
                case 'admin':
                    Admin::create(['user_id' => $user->id]);
                    break;

                case 'service_buyer':
                    ServiceBuyer::create([
                        'user_id' => $user->id,
                        'location' => $validated['buyer_location'] ?? null,
                        'phone_number' => $validated['buyer_phone'] ?? null,
                    ]);
                    break;

                case 'service_provider':
                    ServiceProvider::create([
                        'user_id' => $user->id,
                        'phone_number' => $validated['provider_phone'],
                        'location' => $validated['provider_location'],
                        'provider_type' => $validated['provider_type'],
                        'bio' => $validated['bio'] ?? null,
                        'business_name' => $validated['business_name'] ?? null,
                        'business_address' => $validated['business_address'] ?? null,
                        'avg_rating' => 0,
                        'is_verified' => false, // Default or add to form/validation
                    ]);
                    break;
            }

            DB::commit(); // Everything successful

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Something went wrong
            // Log the error: Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again. Error: ' . $e->getMessage()); // Show detailed error for debugging
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        // Eager load the specific role relationship based on the user's role
        $user = User::with([
            'admin', 'serviceBuyer', 'serviceProvider' // Load all possibilities
        ])->findOrFail($id);

        // Pass necessary data like provider types if needed
        // $providerTypes = ['handyman', 'shop_owner'];

        return view('admin.users.edit', compact('user' /*, 'providerTypes'*/));
    }

    /**
     * Update the specified user and potentially role-specific data in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $originalRole = $user->role;
        $newRole = $request->input('role');

        // --- Enhanced Validation ---
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Ensure email uniqueness check ignores the current user
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed', // Optional password update
            'role' => ['required', 'string', Rule::in(['admin', 'service_buyer', 'service_provider'])],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // --- Service Buyer Fields (Conditional) ---
            'buyer_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role == 'service_buyer')],
            'buyer_phone' => ['nullable', 'string', 'max:20', Rule::requiredIf($request->role == 'service_buyer')],

            // --- Service Provider Fields (Conditional) ---
            'provider_phone' => ['nullable', 'string', 'max:20', Rule::requiredIf($request->role == 'service_provider')],
            'provider_location' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->role == 'service_provider')],
            'provider_type' => ['nullable', Rule::in(['handyman', 'bussiness_owner']), Rule::requiredIf($request->role == 'service_provider')],
            'bio' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'is_verified' => 'nullable|boolean', // Allow updating verification status
        ]);

        DB::beginTransaction();
        try {
            // --- Update User Data ---
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            // Update password only if provided
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle profile photo update
            if ($request->hasFile('profile_image')) {
                // Delete old photo if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $path = $request->file('profile_image')->store('profile_photos', 'public');
                $userData['profile_image'] = $path;
            }

            $user->update($userData);

            // --- Handle Role-Specific Data (Update or Change Role) ---
            if ($originalRole === $newRole) {
                // --- Role NOT changed: Update existing role record ---
                switch ($newRole) {
                    case 'service_buyer':
                        $user->serviceBuyer()->updateOrCreate( // Update or create if somehow missing
                            ['user_id' => $user->id],
                            [
                                'location' => $validated['buyer_location'] ?? null,
                                'phone_number' => $validated['buyer_phone'] ?? null,
                            ]
                        );
                        break;
                    case 'service_provider':
                        $user->serviceProvider()->updateOrCreate( // Update or create if somehow missing
                            ['user_id' => $user->id],
                            [
                                'phone_number' => $validated['provider_phone'],
                                'location' => $validated['provider_location'],
                                'provider_type' => $validated['provider_type'],
                                'bio' => $validated['bio'] ?? null,
                                'business_name' => $validated['business_name'] ?? null,
                                'business_address' => $validated['business_address'] ?? null,
                                // Only update is_verified if provided in the form
                                'is_verified' => $request->has('is_verified') ? $validated['is_verified'] : $user->serviceProvider->is_verified,
                            ]
                        );
                        break;
                    // Admin table has no extra fields in this schema, nothing to update here
                    // case 'admin': break;
                }
            } else {
                // --- Role HAS changed: Delete old record, create new one ---
                // Delete old role record first
                switch ($originalRole) {
                    case 'admin': Admin::where('user_id', $user->id)->delete(); break;
                    case 'service_buyer': ServiceBuyer::where('user_id', $user->id)->delete(); break;
                    case 'service_provider':
                        // Be careful with cascading deletes if provider has services/etc.
                        // You might need more logic here if you don't want related data deleted.
                        ServiceProvider::where('user_id', $user->id)->delete();
                        break;
                }

                // Create new role record
                switch ($newRole) {
                    case 'admin':
                        Admin::create(['user_id' => $user->id]);
                        break;
                    case 'service_buyer':
                        ServiceBuyer::create([
                            'user_id' => $user->id,
                            'location' => $validated['buyer_location'] ?? null,
                            'phone_number' => $validated['buyer_phone'] ?? null,
                        ]);
                        break;
                    case 'service_provider':
                        ServiceProvider::create([
                            'user_id' => $user->id,
                            'phone_number' => $validated['provider_phone'],
                            'location' => $validated['provider_location'],
                            'provider_type' => $validated['provider_type'],
                            'bio' => $validated['bio'] ?? null,
                            'business_name' => $validated['business_name'] ?? null,
                            'business_address' => $validated['business_address'] ?? null,
                            'avg_rating' => 0,
                            // Set verification based on form input or default to false
                            'is_verified' => $request->has('is_verified') ? $validated['is_verified'] : false,
                        ]);
                        break;
                }
            }

            DB::commit(); // All successful

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Something went wrong
            // Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user and cascade deletes to role tables.
     */
    public function destroy($id)
    {
        // Note: ON DELETE CASCADE in the DB schema handles deleting related role records.
        // If not using ON DELETE CASCADE, you'd need to delete them manually here.
        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete profile photo if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Deleting the user will trigger cascade deletes for Admin, ServiceBuyer, ServiceProvider
            // due to the FOREIGN KEY constraints defined in the schema.
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            return redirect()->route('admin.users.index')->with(
                'error',
                $e->getCode() === '23000'
                    ? 'This user cannot be deleted because it has existing orders & services.'
                    : 'An error occurred while deleting the service.'
            );
        }
    }

    // --- show() method remains the same, potentially eager load role data ---
    public function show($id)
    {
        $user = User::with(['admin', 'serviceBuyer', 'serviceProvider'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
}
