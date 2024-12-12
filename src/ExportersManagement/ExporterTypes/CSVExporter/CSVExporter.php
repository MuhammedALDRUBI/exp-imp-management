<?php


namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter;
 
use ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Responders\CSVStreamingResponder;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor; 
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;
use ExpImpManagement\DataProcessors\DataProcessor;
use ExpImpManagement\DataProcessors\ExportableDataProcessors\CSVExportableDataProcessor;
use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Traits\CSVExporterSerilizing;
use ExpImpManagement\ExportersManagement\Interfaces\ExportsCSVImportableData;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;
use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException; 
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;

class CSVExporter extends Exporter
{  
    use CSVExporterSerilizing;

    protected ?PixelExcelExpImpLib $pixelExpImpLib = null; 
    protected ?CSVImportableFileFormatFactory $importableFormatFactory = null;
 
    protected function initExporter() : self
    {
        parent::initExporter();
        $this->initPixelExcelExpImpLib(); 
        return $this;
    }
    protected function initPixelExcelExpImpLib() : void
    {
        $this->pixelExpImpLib = app()->make(PixelExcelExpImpLib::class );
    }
  

    public function useImportableFormatFileFactory(?CSVImportableFileFormatFactory $importableFormatFactory = null) : self
    {
        $this->importableFormatFactory = $importableFormatFactory;
        return $this;
    }

    public function getImportableFormatFileFactory() : ?CSVImportableFileFormatFactory
    {
        return $this->importableFormatFactory ;
    }

    public function exportImportableData(CSVImportableFileFormatFactory $importableFormatFactory , string $documentTitle): JsonResponse | StreamedResponse
    {
        $this->useImportableFormatFileFactory($importableFormatFactory);
        return parent::export($documentTitle);
    }

    protected function initCSVExportableDataProcessor() : CSVExportableDataProcessor
    {
        if($dataProcessorClass = $this->dataProcessorClass)
        { 
            return new $dataProcessorClass($this->importableFormatFactory);
        }

        return new CSVExportableDataProcessor($this->importableFormatFactory);
    }

    protected function setDataProcessorClass(?string  $dataProcessor) : void
    {
        //now on serilizing we always have the DataProcessor type class we need otherwise it will still null
        if($dataProcessor instanceof CSVExportableDataProcessor)
        {
            parent::setDataProcessorClass($dataProcessor);
        }
    }

    protected function useDefaultDataProcessor() : void
    {
        if($this->dataProcessor)
        {
            return;
        }

        if(!$this->importableFormatFactory && $this instanceof ExportsCSVImportableData)
        {
            $this->useImportableFormatFileFactory( $this->getCSVImportableFileFormatFactory() );
        }

        if($this->importableFormatFactory)
        {
            $this->setDataProcessor(  $this->initCSVExportableDataProcessor() );
        } 
        
    }
    
    protected function processDataCollection()
    {
        $this->useDefaultDataProcessor();
        parent::processDataCollection();
    }
 
    protected function doesItHaveRelationshipsToExport() : bool
    {
        if(!$this->importableFormatFactory && $this instanceof ExportsCSVImportableData)
        {
            $this->importableFormatFactory = $this->getCSVImportableFileFormatFactory();
        }

        if($this->importableFormatFactory)
        {
            return $this->importableFormatFactory->doesItHaveRelationships();
        }

        return parent::doesItHaveRelationshipsToExport();
    }

    protected function getStreamingResponder(): StreamingResponder
    {
        return new CSVStreamingResponder($this->pixelExpImpLib);
    }

    protected function getDataFileExtension() : string
    {
        return "csv";
    }

    /**
     * @return string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws Exception
     */
    protected function uploadDataFileToTempPath() : string
    {
        $tempFolderPath = $this->filesProcessor->HandleTempFileToCopy(
                                                                        $this->pixelExpImpLib->data($this->DataCollection)->export( $this->fileFullName ),
                                                                        $this->fileFullName
                                                                    )->copyToTempPath();
        return $tempFolderPath . $this->fileFullName;
    }

}
