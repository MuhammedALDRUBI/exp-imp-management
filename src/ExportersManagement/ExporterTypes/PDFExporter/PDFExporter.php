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
class PDFExporter extends Exporter
{ 

    protected ?PixelPdfNeedsProvider $pdfLib = null; 
    protected ?string $viewTemplateRelativePath = null;
    /**
     * @throws MpdfException
     * @throws Exception
     */
    public function __construct(?string $modelClass = null) 
    {
        parent::__construct( $modelClass) ;
        $this->initPDFLib();
    }

    protected function setUnserlizedProps($data)
    {
        parent::setUnserlizedProps($data);
        $this->setViewTemplateRelativePath($data["viewTemplateRelativePath"]);
    }

    public function __wakeup()
    { 
        $this->initPDFLib();
    }
    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) || !array_key_exists("viewTemplateRelativePath" , $data);
    }

    protected function getSerlizingProps() : array
    {
        $parentProps = parent::getSerlizingProps();
        $parentProps[] = "viewTemplateRelativePath";
        return $parentProps;
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
