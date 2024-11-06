<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Responders;

use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use Illuminate\Http\JsonResponse;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException; 
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVStreamingResponder extends StreamingResponder
{
    protected ?PixelExcelExpImpLib $pixelExpImpLib = null;

    public function __construct(PixelExcelExpImpLib $pixelExcelExpImpLib)
    {
        $this->pixelExpImpLib = $pixelExcelExpImpLib;
    }

    /**
     * @return BinaryFileResponse|StreamedResponse|JsonResponse| string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function respond():BinaryFileResponse | StreamedResponse | JsonResponse| string
    {
        return $this->pixelExpImpLib->data($this->DataCollection)->download($this->FileFullName);
    }
}
