<?php


namespace ExpImpManagement\ExportersManagement\Exporter;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\ExportedFilesProcessor;
use ExpImpManagement\ExportersManagement\Exporter\Traits\DataCustomizerMethods;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterAbstractMethods;  
use ExpImpManagement\ExportersManagement\Responders\Responder;
use CustomFileSystem\CustomFileHandler;
use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterSerilizing;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ResponderMethods; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use JsonSerializable;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Exporter  implements JsonSerializable
{
    use DataCustomizerMethods  , ExporterAbstractMethods , ResponderMethods , ExporterSerilizing;

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
     * Final File Which Be Uploaded To Storage (Data File OR Zip File If it Needs To A Compression)
     */
    protected string $finalFilePath = "";

    protected string $title;
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

    public function useTheSameDocumentTitle() : self
    {
        $this->outoutUniqueDocumentTitle = false;
        return $this;
    }

    public function useUniqueDocumentTitle() :  self
    {
        $this->outoutUniqueDocumentTitle = true;
        return $this;
    }

    public function composeFileFullName() : string
    {
        return $this->fileName . "." . $this->getDataFileExtension();
    }

    protected function setFileFullName() : self
    { 
        $this->fileFullName =  $this->composeFileFullName(); 
        return $this;
    }
    
    /**
     * return Only File Name (Document Title + Date  , Doesn't Contain The Extension )
     *To Get Full name With Extension use $this->fileName
     * @return string
     */
    public function composeFileName(string $documentTitle) : string
    {
        $name = $this->sanitizeFileCustomName($documentTitle);
        return $this->outoutUniqueDocumentTitle 
               ? 
               Str::slug( $name , "_") .  date("_Y_m_d_his") 
               :
               $name;
    }

    protected function sanitizeFileCustomName(string $name) : string
    {
        return explode("." , $name)[0];
    }

    /**
     * @param string $name
     * @param string $extension
     * @return $this
     * @throws Exception 
     */
    protected function setFileName(string $documentTitle ) : self
    {
        $this->fileName =  $this->composeFileName($documentTitle) ;
        return $this;
    }
    /**
     * @return $this
     */
    protected function setFileNames(string $documentTitle) : void
    {
        $this->setFileName($documentTitle)->setFileFullName();
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


    // protected function processDataFilePath(string $DataFileContainerFolderPath) : string
    // {
    //     return CustomFileHandler::processFolderPath($DataFileContainerFolderPath) . $this->fileFullName;
    // }

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
     * Returns Final File's Path In storage
     * @throws Exception
     */
    // protected function uploadFinalFile() : string
    // { 
    //     dd(file_exists($this->finalFilePath));
    //     return $this->filesProcessor->uploadToStorage($this->finalFilePath);
    // }

    /**
     * @return string
     * @throws JsonException
     * @throws Exception
     */
    protected function prepareDataFileToUpload() : string
    {
        
        //return DataFile real temp path
         return $this->initExporter()
                     ->PrepareExporterData()
                     ->setFilesProcessor()
                     ->uploadDataFileToTempPath();
        // return $this->processDataFilePath($DataFilePath);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function exportingJobFun() : string
    {
        $this->finalFilePath = $this->prepareDataFileToUpload();
        $this->filesProcessor->informExportedDataFilesInfoManager($this->finalFilePath);
        // $this->uploadFinalFile();
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