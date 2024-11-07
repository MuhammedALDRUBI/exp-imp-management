<?php


namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter;
 
use ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Responders\CSVStreamingResponder;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor; 
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException; 

abstract class CSVExporter extends Exporter
{
    protected ?PixelExcelExpImpLib $pixelExpImpLib = null;

    public function __construct()
    {
        parent::__construct();
        $this->initPixelExcelExpImpLib();
    }

    protected function initPixelExcelExpImpLib() : void
    {
        $this->pixelExpImpLib = app()->make(PixelExcelExpImpLib::class );
    }
    /** * @var callable $FinalDataArrayMappingFun */
    protected  $FinalDataArrayMappingFun = null;
    /**
     * @param callable $mappingFun
     * @return $this
     */
    public function mapOnFinalDataArray(callable $mappingFun) : self
    {
        $this->FinalDataArrayMappingFun = $mappingFun;
        return $this;
    } 

    /**
     * @return DataCustomizerMethods|Exporter
     * @throws Exception
     */
    protected function processDataCollection(DataArrayProcessor $finalDataArrayProcessor) : self
    {
        $this->DataCollection =  $finalDataArrayProcessor->getProcessedData($this->DataCollection);
        return $this;
    }
    protected function getFinalDataArrayProcessor(): DataArrayProcessor
    {
        return new DataArrayProcessor();
    }
  
    /**
     * @return array
     * This method is useful to determine the desired columns of model
     * Note  : if the result has '*' as the first element ... That means we want all retrieved columns of the model (not all actual columns ... ONLY Retrieved Columns)
     */
    protected function getModelDesiredFinalColumns() : array  
    {
        return ['*'];
    }
    /**
     * @return DataArrayProcessor
     */
    protected function initFinalDataArrayProcessor() : DataArrayProcessor
    {  
        return $this->getFinalDataArrayProcessor()
                    ->setModelDesiredFinalDefaultColumnsArray($this->getModelDesiredFinalColumns())
                    ->setFinalDataArrayMappingFun($this->FinalDataArrayMappingFun); 
    }

    protected function PrepareExporterData() : self
    {
        parent::PrepareExporterData();
        
        $finalDataArrayProcessor = $this->initFinalDataArrayProcessor();
        $this->processDataCollection($finalDataArrayProcessor);
        return $this;
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
    protected function setDataFileToExportedFilesProcessor() : string
    {
        return $this->filesProcessor->HandleTempFileToCopy(
                    $this->pixelExpImpLib->data($this->DataCollection)->export( $this->fileFullName ),
                    $this->fileFullName
                )->copyToTempPath();
    }

}
