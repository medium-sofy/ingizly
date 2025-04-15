protected $routeMiddleware = [
    // ...
    'verified.email' => \App\Http\Middleware\EnsureEmailIsVerified::class,
];