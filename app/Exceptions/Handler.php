<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use RuntimeException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    // Other properties and methods...

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
   
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthenticated user.',
            ], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            return $e;
        });
    }
}
