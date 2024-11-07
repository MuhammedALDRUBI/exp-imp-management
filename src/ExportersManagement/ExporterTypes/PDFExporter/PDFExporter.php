<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter;

use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter\Responders\PDFStreamingResponder;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;  
use Mpdf\MpdfException;
use PixelDomPdf\Interfaces\PixelPdfNeedsProvider; 
use Illuminate\Contracts\View\View;

/**
 * @prop PDFStreamingResponder |  $responder
 */
abstract class PDFExporter extends Exporter
{ 

    protected ?PixelPdfNeedsProvider $pdfLib = null; 
    /**
     * @throws MpdfException
     * @throws Exception
     */
    function __construct()
    {
        parent::__construct();
        $this->initPDFLib();
    }

    abstract protected function getViewTemplateRelativePath() : string;

    /**
     * @return $this
     */
    protected function initPDFLib() : self
    {
        $this->pdfLib = app()->make(PixelPdfNeedsProvider::class);
        return $this;
    }
  
    /**
     * Handle the data collection as you want in the view 
     */
    protected function getViewToRender() : View
    {
        return view($this->getViewTemplateRelativePath() , $this->DataCollection);
    }
    
    protected function passViewToPDFLib() : self
    {
        $this->pdfLib->loadView($this->getViewToRender());
        return $this;
    }
    
    /**
     * @return $this
     * @throws JsonException
     * 
     * this method allows the child classes to do somthings after setting DataCollection
     */
    protected function PrepareExporterData() : self
    {
        parent::PrepareExporterData();
        return $this->passViewToPDFLib();
    }

    protected function getStreamingResponder(): StreamingResponder
    { 
        return new PDFStreamingResponder( $this->pdfLib );
    }
  
    protected function getDataFileExtension() : string
    {
        return "pdf";
    }

    /**
     * @return string
     * @throws MpdfException
     * @throws Exception
     */
    protected function setDataFileToExportedFilesProcessor() : string
    {
        return $this->filesProcessor->HandleTempFileContentToCopy( $this->pdfLib->output() , $this->fileFullName )
                                    ->copyToTempPath();
    }

}
