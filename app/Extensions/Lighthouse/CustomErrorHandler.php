<?php

namespace App\Extensions\Lighthouse;

use GraphQL\Error\Error;
use Illuminate\Database\QueryException;
use Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions;
use Nuwave\Lighthouse\Execution\ErrorHandler;

class CustomErrorHandler implements ErrorHandler
{

    /**
     * Handle Exceptions that implement Nuwave\Lighthouse\Exceptions\RendersErrorsExtensions
     * and add extra content from them to the 'extensions' key of the Error that is rendered
     * to the User.
     *
     * @param  Error  $error
     * @param  \Closure  $next
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function handle(Error $error, \Closure $next): array
    {
        $underlyingException = $error->getPrevious();

        if ($underlyingException && $underlyingException instanceof RendersErrorsExtensions) {
            $error_code = (new \ReflectionClass($underlyingException))->hasMethod('getGraphQLCode') ? $underlyingException->getGraphQLCode() : 30000;

            if ($error_code === 30000) {
                \Log::error('graphql-error', [
                    'message' => $error->message,
                    'source'  => $error->getSource(),
                    'path'    => $error->getPath(),
                    //'nodes'    => $error->nodes,
                ]);
            } else {
                \Log::info('graphql-error', [
                    'message' => $error->message,
                    'source'  => $error->getSource(),
                    'path'    => $error->getPath(),
                    //'nodes'    => $error->nodes,
                ]);
            }

            // Reconstruct the error, passing in the extensions of the underlying exception
            $error = new Error($error->message, $error->nodes, $error->getSource(), $error->getPositions(),
                $error->getPath(), $underlyingException, array_merge($underlyingException->extensionsContent(), [
                    'error_code' => $error_code
                ]));
        } else {
            \Log::error('未知错误', [$underlyingException, $error]);
        }

        return $next($error);
    }
}
