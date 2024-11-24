<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter;

use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter\Responders\PDFStreamingResponder;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;
use ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter\Traits\PDFExporterSerilizing;
use ExpImpManagement\ExportersManagement\Interfaces\SelfConstructablePDFExporter;
use Mpdf\MpdfException;
use PixelDomPdf\Interfaces\PixelPdfNeedsProvider; 
use Illuminate\Contracts\View\View;

/**
 * @prop PDFStreamingResponder |  $responder
 */
class PDFExporter extends Exporter
{ 

    use PDFExporterSerilizing;

    protected ?PixelPdfNeedsProvider $pdfLib = null; 
    protected ?string $viewTemplateRelativePath = null;
 
    protected function initExporter() : self
    {
        parent::initExporter();

        if($this instanceof SelfConstructablePDFExporter)
        {
            $templatePath = $this->getViewRelevantPathForSelfConstructing();
            $this->setViewTemplateRelativePath($templatePath);
        }

        $this->initPDFLib();
        return $this;
    }

    public function setViewTemplateRelativePath(string $viewTemplateRelativePath) : self
    {
        $this->viewTemplateRelativePath = $viewTemplateRelativePath;
        return $this;
    }

    public function getViewTemplateRelativePath() : ?string
    {
        return $this->viewTemplateRelativePath;
    }

    protected function requireViewTemplateRelativePath() : string
    {
        return $this->getViewTemplateRelativePath() ??
               throw new Exception("The view template path is not set while it is required for pdf rendering");
    }

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
        return view($this->requireViewTemplateRelativePath() , ["data" => $this->DataCollection ]);
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

        //passing it after data is set manually or fetched by PrepareExporterData parent's method
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
    protected function uploadDataFileToTempPath() : string
    {
        $tempFolderPath = $this->filesProcessor->HandleTempFileContentToCopy( $this->pdfLib->output() , $this->fileFullName )->getCopiedTempFilesFolderPath();
        return $tempFolderPath . $this->fileFullName;
    }

}
