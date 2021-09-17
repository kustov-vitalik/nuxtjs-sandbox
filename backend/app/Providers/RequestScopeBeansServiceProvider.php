<?php

namespace App\Providers;

use App\Common\Dto\CreateUserRequest;
use App\Common\Dto\JsonRequestBody;
use App\Common\Dto\Pageable;
use App\Common\Exception\InvalidJsonBodyException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class RequestScopeBeansServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(Pageable::class, static function (Application $application) {
            $request = $application->get(Request::class);
            if (!$request instanceof Request) {
                throw new RuntimeException("Request scope required!");
            }

            return Pageable::ofRequest($request);
        });

        $this->app->beforeResolving(JsonRequestBody::class, function (string $className) {
            $this->app->bind($className, function () use ($className) {
                $request = $this->app->get(Request::class);
                if (!$request instanceof Request) {
                    throw new RuntimeException("Request scope required!");
                }

                try {
                    $reflectionClass = new \ReflectionClass($className);
                    $reflectionMethod = $reflectionClass->getMethod('ofRequest');
                    return $reflectionMethod->invoke(null, $request);
                } catch (\ReflectionException $e) {
                    throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
                } catch (InvalidJsonBodyException $e) {
                    // throws exception internally
                    abort(Response::HTTP_BAD_REQUEST, $e->getMessage());
                }
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
