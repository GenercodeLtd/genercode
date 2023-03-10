<?php

namespace GenerCode\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

class JsonMiddleware
{
    /**
     * The Response Factory our app uses
     * 
     * @var ResponseFactory
     */
    protected $factory;

    /**
     * JsonMiddleware constructor.
     * 
     * @param ResponseFactory $factory
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // If the response is not strictly a JsonResponse, we make it
        if (!$response instanceof JsonResponse AND $request->headers->set('Accept', 'application/json')) {
            $response = $this->factory->json(
                $response->content(),
                $response->status(),
                $response->headers->all()
            );
        }
        
        return $response;
    }
}