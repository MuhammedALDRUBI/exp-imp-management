<?php

namespace ExpImpManagement\ImportersManagement\Responders;

use Illuminate\Http\JsonResponse;

abstract class Responder
{
    /**
     * @return JsonResponse
     */
    abstract public function respond() : JsonResponse;

}
