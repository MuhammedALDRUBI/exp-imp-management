<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter\Responders;

use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Illuminate\Http\JsonResponse; 
use Mpdf\MpdfException;
use PixelDomPdf\Interfaces\PixelPdfNeedsProvider;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFStreamingResponder extends StreamingResponder
{
    protected ?PixelPdfNeedsProvider $PDFLib = null;  

    public function __construct(PixelPdfNeedsProvider $PDFLib )
    {
        $this->PDFLib = $PDFLib ;  
    } 

    /**
     * @return BinaryFileResponse|StreamedResponse|JsonResponse|string
     * @throws MpdfException
     */
    public function respond():BinaryFileResponse | StreamedResponse | JsonResponse| string
    {
        return $this->PDFLib->stream( $this->FileFullName );
    }
}
