<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use AIGenerate\Services\Exceptions\Loggers\Contracts\ExceptionServiceContract;
use Throwable;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Example for response examples value"
 * )
 * @OA\PathItem(path="/api")
 * @OA\Schema(
 *  schema="Result",
 *  title="Sample schema for using references",
 * 	@OA\Property(
 *      property="message",
 *      type="string",
 *      example="A simple of response message"
 *    ),
 * 	@OA\Property(
 *     property="data",
 *     type="Object",
 *    )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearer_token",
 *     type="http",
 *     in="header",
 *     scheme="bearer",
 * ),
 */
class BaseController extends Controller
{

    /**
     * @throws \Throwable
     */
    protected function transaction(callable $callback, callable $error = null)
    {
        try {
            DB::beginTransaction();
            $result = $callback();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            $result = $this->catchException($throwable, $error);
        }
        return $result;
    }

    /**
     * @throws \Throwable
     */
    protected function catchException(Throwable $throwable, $error)
    {
        if (is_callable($error)) {
            $service = app()->make(ExceptionServiceContract::class);
            $service->log($throwable);
            return $error($throwable);
        } else {
            if ($error) {
                return $error;
            }
            throw $throwable;
        }
    }

    protected function run(callable $callback, callable $error = null)
    {
        try {
            $result = $callback();
        } catch (Throwable $throwable) {
            $result = $this->catchException($throwable, $error);
        }
        return $result;
    }

}
