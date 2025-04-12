<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Base query
        $query = User::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");

                // Add these conditionals for fields that might not exist in your schema
                if (Schema::hasColumn('users', 'username')) {
                    $q->orWhere('username', 'LIKE', "%{$search}%");
                }
                if (Schema::hasColumn('users', 'phone')) {
                    $q->orWhere('phone', 'LIKE', "%{$search}%");
                }
            });
        }

        // Apply date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply role filter (modified for enum field)
        if ($request->has('role') && $request->role != 'All Roles') {
            $query->where('role', $request->role);
        }

        // Apply location filter
        if ($request->has('location') && $request->location != 'All Locations') {
            $query->where('location', $request->location);
        }

        // Apply status filter if status column exists
        if (Schema::hasColumn('users', 'status') && $request->has('status') && $request->status != 'All Statuses') {
            $query->where('status', strtolower($request->status));
        }



        // Get paginated users
        $users = $query->latest()->paginate(10);

        // Append query parameters to pagination links
        $users->appends($request->all());

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,service_buyer,service_provider',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'last_login' => null
        ];

        // Add these fields if they exist in your schema
        if (Schema::hasColumn('users', 'username') && $request->has('username')) {
            $request->validate(['username' => 'required|string|max:50|unique:users']);
            $userData['username'] = $request->username;
        }

        if (Schema::hasColumn('users', 'phone') && $request->has('phone')) {
            $userData['phone'] = $request->phone;
        }

        if (Schema::hasColumn('users', 'location') && $request->has('location')) {
            $userData['location'] = $request->location;
        }

        if (Schema::hasColumn('users', 'status') && $request->has('status')) {
            $request->validate(['status' => 'required|in:active,suspended,pending']);
            $userData['status'] = $request->status;
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $userData['profile_image'] = $path; // Adjusted to match your schema
        }

        $user = User::create($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);



        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|string|in:admin,service_buyer,service_provider',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Add these validations if the columns exist
        if (Schema::hasColumn('users', 'username')) {
            $rules['username'] = 'required|string|max:50|unique:users,username,'.$id;
        }

        if (Schema::hasColumn('users', 'phone')) {
            $rules['phone'] = 'nullable|string|max:20';
        }

        if (Schema::hasColumn('users', 'location')) {
            $rules['location'] = 'nullable|string|max:255';
        }

        if (Schema::hasColumn('users', 'status')) {
            $rules['status'] = 'required|in:active,suspended,pending';
        }

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        // Add these fields if they exist and are provided
        if (Schema::hasColumn('users', 'username') && $request->has('username')) {
            $userData['username'] = $request->username;
        }

        if (Schema::hasColumn('users', 'phone') && $request->has('phone')) {
            $userData['phone'] = $request->phone;
        }

        if (Schema::hasColumn('users', 'location') && $request->has('location')) {
            $userData['location'] = $request->location;
        }

        if (Schema::hasColumn('users', 'status') && $request->has('status')) {
            $userData['status'] = $request->status;
        }

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $userData['profile_image'] = $path; // Adjusted to match your schema
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete profile photo if exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
