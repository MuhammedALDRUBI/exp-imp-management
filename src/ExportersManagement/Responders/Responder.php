<?php

namespace ExpImpManagement\ExportersManagement\Responders;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Responder
{
    protected Collection | LazyCollection | null $DataCollection = null;

    public function setDataCollectionToExport(Collection | LazyCollection | null $DataCollection = null) : self
    {
        $this->DataCollection = $DataCollection;
        return $this;
    }

    public function geTDataCollection() : Collection | LazyCollection | null
    {
        return $this->DataCollection;
    }
    
    abstract public function respond() :BinaryFileResponse | StreamedResponse | JsonResponse | string;

}
