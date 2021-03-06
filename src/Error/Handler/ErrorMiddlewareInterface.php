<?php

namespace Digia\GraphQL\Error\Handler;

use Digia\GraphQL\Execution\ExecutionContext;
use Digia\GraphQL\Execution\ExecutionException;

interface ErrorMiddlewareInterface
{
    /**
     * @param \Throwable $exception
     * @param callable   $next
     * @return mixed
     */
    public function handleError(\Throwable $exception, callable $next);

    /**
     * @param ExecutionException $exception
     * @param ExecutionContext   $context
     * @param callable           $next
     * @return mixed
     */
    public function handleExecutionError(ExecutionException $exception, ExecutionContext $context, callable $next);
}
