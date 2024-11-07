<?php


namespace ExpImpManagement\ExportersManagement\Exporter;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\ExportedFilesProcessor;
use ExpImpManagement\ExportersManagement\Exporter\Traits\DataCustomizerMethods;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterAbstractMethods;  
use ExpImpManagement\ExportersManagement\Responders\Responder;
use CustomFileSystem\CustomFileHandler;
use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Traits\ResponderMethods; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Exporter
{
    use DataCustomizerMethods  , ExporterAbstractMethods , ResponderMethods;

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
    public function __construct() {}

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

    public function composeFileFullName() : string
    {
        return $this->fileName . "." . $this->getDataFileExtension();
    }
    /**
     * return Only File Name (Document Title + Date  , Doesn't Contain The Extension )
     *To Get Full name With Extension use $this->fileName
     * @return string
     */
    public function composeFileName() : string
    {
        return Str::slug( $this->getDocumentTitle() , "_") .  date("_Y_m_d_his") ;
    }

    /**
     * @return $this
     */
    protected function setDefaultFileName() : void
    {
        $this->fileName =  $this->composeFileName() ;
        $this->fileFullName =  $this->composeFileFullName(); 
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function initExporter() : self
    {
        $this->setDefaultFileName();

        if( $this->DataCollection == null) //if there is a DataCollection ... it is set manually in the controller context class ... no need to fetch it twice
        {
            $this->prepareQueryBuilder();
            $this->setNeededDataCount();
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string $extension
     * @return $this
     * @throws Exception
     * This Method is used to change file name from controller context ... but it is mainly changed by child class
     * by getFileName method in the initExporter method of object
     */
    public function setCustomFileName(string $name , string $extension) : self
    {
        $this->fileName = $name  ;
        $this->fileFullName = $name . "." . $extension;
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


    protected function processDataFilePath(string $DataFileContainerFolderPath) : string
    {
        return CustomFileHandler::processFolderPath($DataFileContainerFolderPath) . $this->fileFullName;
    }

    protected function generateFileFinalURL(string $fileName) : string
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
    protected function uploadFinalFile() : string
    {
        return $this->filesProcessor->ExportedFilesStorageUploading($this->finalFilePath);
    }

    /**
     * @return string
     * @throws JsonException
     * @throws Exception
     */
    protected function prepareDataFileToUpload() : string
    {
         $this->initExporter()
                    ->PrepareExporterData()
                    ->setFilesProcessor();

        $DataFilePath =  $this->setDataFileToExportedFilesProcessor();
        return $this->processDataFilePath($DataFilePath);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function exportingJobFun() : string
    {
        $this->finalFilePath = $this->prepareDataFileToUpload();
        $this->uploadFinalFile();
        return $this->generateFileFinalURL(
                    $this->filesProcessor->getFileDefaultName($this->finalFilePath) // geting the name after the child class handled it by setDataFileToExportedFilesProcessor()
                );
    }


    /**
     * @return JsonResponse|StreamedResponse
     * @throws JsonException | Exception
     */
    public function export() : JsonResponse | StreamedResponse
    {
        try {
            $this->initExporter();
            return $this->getConvenientResponder()->respond(); 
        }catch(Exception $e)
        {
            return Response::error([$e->getMessage()]);
        }
    }

}