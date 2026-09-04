<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Ensan Charity System API",
 *     version="1.0.0"
 * )
 * @OA\Server(
 *     url="/System.Ensan-main/public",
 *     description="Local MAMP server"
 * )
 */
class OpenApi
{
    /**
     * @OA\Get(
     *     path="/api/v1/health",
     *     tags={"Health"},
     *     summary="Health check",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function health()
    {
    }
}
