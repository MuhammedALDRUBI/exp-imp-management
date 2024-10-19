<?php

namespace ExpImpManagement\ExportersManagement\Exporter\ExportersMainTypes;


use ExpImpManagement\ExportersManagement\ExportedFilesHandler\ExportedFilesProcessors\ExportedFilesProcessor;
use ExpImpManagement\ExportersManagement\ExportedFilesHandler\FilesExportingProcessManager;
use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use Exception;
use ExpImpManagement\ExportersManagement\Interfaces\FileExportingInterfaces\MustExportFiles;
use ExpImpManagement\ExportersManagement\Interfaces\FileExportingInterfaces\SupportRelationshipsFilesExporting;

abstract class ImportableDataExporter extends Exporter
{
    protected ?FilesExportingProcessManager $exportingProcessManager;

    protected function setModelFilesColumnsArrayToManager() : self
    {
        if(!$this->MustExportFiles()){return $this;}

        /** * @var MustExportFiles | ImportableDataExporter $this */
        $this->exportingProcessManager->setModelFilesColumnsArray(
                                            $this->getModelFilesColumnsArray()
                                        );
        return $this;
    }

    protected function setModelRelationshipsFilesColumnsArrayToManager() : self
    {
        if(!$this->SupportRelationshipsFilesExporting()){return $this;}

        /** * @var SupportRelationshipsFilesExporting|ImportableDataExporter $this */
        $this->exportingProcessManager->setModelRelationshipsFilesColumnsArray(
                                            $this->getModelRelationshipsFilesColumnsArray()
                                        );
        return $this;
    }


    protected function getFilesProcessorForManager() : ExportedFilesProcessor
    {
        return $this->filesProcessor->setCopiedTempFilesFolderName($this->fileName);
    }

    /**
     * @return FilesExportingProcessManager
     * @throws Exception
     */
    public function initExportingProcessManager(): FilesExportingProcessManager
    {
        $this->exportingProcessManager = new FilesExportingProcessManager();

        $this->exportingProcessManager->setExportedFilesProcessor( $this->getFilesProcessorForManager())
            ->setDataCollection($this->DataCollection);
        $this->setModelFilesColumnsArrayToManager()->setModelRelationshipsFilesColumnsArrayToManager();
        return $this->exportingProcessManager;
    }

    /**
     * @return string
     * @throws JsonException
     * @throws Exception
     */
    public function exportingJobAllDataAndFilesFun() : string
    {
        //Extending Parent Method and getting Exported Data File Real Path
        $DataFilePath = parent::exportingJobAllDataAndFilesFun();
        if( !$this->SupportRelationshipsFilesExporting() &&  !$this->MustExportFiles() ) { return $DataFilePath;  }
        return $this->initExportingProcessManager()->handleFileOperations($DataFilePath);
    }

}
