<?php

namespace App\Http\Middleware;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\UserResoure;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // usePage
        return [
            ...parent::share($request),
            'attachmentExtensions' => StorePostRequest::$extensions,
            'auth' => [
                'user' => $request->user() ? new UserResoure($request->user()) : null,
            ],
        ];
    }
}
