<?php


namespace ExpImpManagement\ExportersManagement\Exporter;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\ExportedFilesProcessor;
use ExpImpManagement\ExportersManagement\Exporter\Traits\DataCustomizerMethods;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterAbstractMethods;  
use ExpImpManagement\ExportersManagement\Responders\Responder;
use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterSerilizing;
use ExpImpManagement\ExportersManagement\Exporter\Traits\FileNameProccessing;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ResponderMethods; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use JsonSerializable;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Exporter  implements JsonSerializable
{
    use DataCustomizerMethods  , ExporterAbstractMethods , ResponderMethods , ExporterSerilizing , FileNameProccessing;

    /**
     * @var string
     * Without Extension
     */
    protected string $fileName = "";

    /**
     * @var string
     * With Extension
     */
    protected string $fileFullName ="" ;
    protected bool $outoutUniqueDocumentTitle = true;

    /**
     * @var string
     * Final File path in temp folder 
     */
    protected string $finalFilePath = "";
 
    protected ?ExportedFilesProcessor $filesProcessor = null; 


    /**
     * @return $this
     */
    protected function setFilesProcessor(): self
    {
        if($this->filesProcessor){return $this;}
        $this->filesProcessor = new ExportedFilesProcessor();
        return $this;
    }


    /**
     * @throws Exception
     */
    public function __construct(?string $modelClass = null) 
    {
        $this->setModelClassOptinally($modelClass);
    }

    /**
     * @return Responder
     * @throws Exception
     */
    protected function getConvenientResponder() : Responder
    {
        if( $this->DoesHaveBigData() )
        {
            return $this->initJobDispatcherJSONResponder();
        } 
        return $this->initStreamingResponder();
    }
    /**
     * @return $this
     * @throws Exception
     */
    protected function initExporter() : self
    { 
        if( $this->DataCollection == null) //if there is a DataCollection ... it is set manually in the controller context class ... no need to fetch it twice
        { 
            $this->prepareQueryBuilder();
            $this->setNeededDataCount();
        }
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
        return $this->setDefaultDataCollection();
    }
 
    protected function generateFileAssetURL(string $fileName) : string
    {
        return URL::temporarySignedRoute(
                "exported-file-downloading" ,
                      now()->addDays(ExportedDataFilesInfoManager::ValidityIntervalDayCount)->getTimestamp() ,
                     ["fileName" => $fileName]
                );
    }
 
    /**
     * @return string
     * @throws JsonException
     * @throws Exception
     * 
     * 
     * return DataFile real temp path
     */
    protected function prepareDataFileToUpload() : string
    { 
         return $this->initExporter()
                     ->PrepareExporterData()
                     ->setFilesProcessor()
                     ->uploadDataFileToTempPath(); 
    }

    /**
     * @return string
     * @throws Exception
     */
    public function exportingJobFun() : string
    {
        $this->finalFilePath = $this->prepareDataFileToUpload();
        $this->filesProcessor->informExportedDataFilesInfoManager($this->finalFilePath); 

        return $this->generateFileAssetURL(
                                                // geting the name after the child class handled it by setDataFileToExportedFilesProcessor()
                                                $this->filesProcessor->getFileDefaultName($this->finalFilePath) 
                                            );
    }
 
    /**
     * @return JsonResponse|StreamedResponse
     * @throws JsonException | Exception
     */
    public function export(string $documentTitle) : JsonResponse | StreamedResponse
    {
        try {
            $this->setFileNames($documentTitle);
            $this->initExporter();
            return $this->getConvenientResponder()->respond(); 
        }catch(Exception $e)
        {
            return Response::error([$e->getMessage()]);
        }
    }

}