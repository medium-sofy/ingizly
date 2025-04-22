<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Review;
use App\Models\Order;
use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    // dashboard function to collect statistics and return view & pass the parameters
    public function dashboard()
    {
        // Get counts for dashboard overview
        $totalUsers = User::count();
        $activeServices = Service::where('status', 'active')->count();
        $newReviews = Review::whereMonth('created_at', Carbon::now()->month)->count();
        $totalSpent = Payment::where('payment_status', 'successful')->sum('amount');
        $spentLastMonth = Payment::whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('amount');
        $spentPercentageChange = $spentLastMonth > 0 ? round((($totalSpent - $spentLastMonth) / $spentLastMonth) * 100) : 0;
        // Calculate percentage changes from last month
        $usersLastMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $userPercentageChange = $usersLastMonth > 0 ? round((($totalUsers - $usersLastMonth) / $usersLastMonth) * 100) : 0;

        $servicesLastMonth = Service::where('status', 'active')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
        $servicePercentageChange = $servicesLastMonth > 0 ? round((($activeServices - $servicesLastMonth) / $servicesLastMonth) * 100) : 0;

        $reviewsLastMonth = Review::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $reviewPercentageChange = $reviewsLastMonth > 0 ? round((($newReviews - $reviewsLastMonth) / $reviewsLastMonth) * 100) : 0;

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get pending approvals
        $pendingServices = Service::with(['provider.user'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeServices',
            'newReviews',
            'userPercentageChange',
            'servicePercentageChange',
            'reviewPercentageChange',
            'recentActivities',
            'pendingServices',
            'totalSpent',
            'spentPercentageChange'
        ));
    }
    private function getRecentActivities()
    {
        // Combine recent users, services, reviews and reports
        $recentUsers = User::select(
            DB::raw("'New user registration' as activity"),
            'created_at as timestamp',
            DB::raw("'user' as type")
        )->latest()->take(5);

        $recentServices = Service::select(
            DB::raw("'Service approved' as activity"),
            'updated_at as timestamp',
            DB::raw("'service' as type")
        )->where('status', 'active')->latest('updated_at')->take(5);

        $recentReviews = Review::select(
            DB::raw("'New review submitted' as activity"),
            'created_at as timestamp',
            DB::raw("'review' as type")
        )->latest()->take(5);

        $recentReports = DB::table('violations')->select(
            DB::raw("'Reports submitted' as activity"),
            'created_at as timestamp',
            DB::raw("'report' as type")
        )->latest()->take(5);

        // Union all queries and get latest 10 activities
        return $recentUsers
            ->union($recentServices)
            ->union($recentReviews)
            ->union($recentReports)
            ->latest('timestamp')
            ->take(10)
            ->get();
    }
    public function approveService($id)
    {
        $service = Service::findOrFail($id);
        $service->status = 'active';
        $service->save();

        // Create notification for service provider
        $this->createServiceNotification($service->provider_id, 'Service Approved', 'Your service has been approved by admin.');

        return redirect()->back()->with('success', 'Service has been approved');
    }

    /**
     * Reject a pending service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectService($id)
    {
        $service = Service::findOrFail($id);
        $service->status = 'inactive';
        $service->save();

        // Create notification for service provider
        $this->createServiceNotification($service->provider_id, 'Service Rejected', 'Your service has been rejected by admin.');

        return redirect()->back()->with('success', 'Service has been rejected');
    }

    /**
     * Create a notification for a user.
     *
     * @param  int  $userId
     * @param  string  $title
     * @param  string  $content
     * @return void
     */
    private function createServiceNotification($userId, $title, $content)
    {
        DB::table('notifications')->insert([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'notification_type' => 'system',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Show all users.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show all services.
     *
     * @return \Illuminate\View\View
     */
    public function services()
    {
        $services = Service::with(['category', 'provider.user'])->latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show all categories.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        $categories = Category::withCount('services')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show all reviews.
     *
     * @return \Illuminate\View\View
     */
    public function reviews()
    {
        $reviews = Review::with(['service', 'buyer.user'])->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show reports page.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        // You can customize this based on what kind of reports you want to show
        $violations = DB::table('violations')
            ->join('users', 'violations.user_id', '=', 'users.id')
            ->join('services', 'violations.service_id', '=', 'services.id')
            ->select('violations.*', 'users.name as reporter_name', 'services.title as service_title')
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', compact('violations'));
    }
}
