<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors;


use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\Traits\ExportedDataFilesInfoManagerMethods;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use Exception;

class ExportedFilesProcessor extends TemporaryFilesProcessor
{

    use  ExportedDataFilesInfoManagerMethods;

    /**
     * @param string $filePath
     * @return string
     * @throws Exception
     */
    public function ExportedFilesStorageUploading(string $filePath) : string
    {
        $fileNewRelevantPath = $this->uploadToStorage($filePath);
        $this->informExportedDataFilesInfoManager($fileNewRelevantPath);
        return $fileNewRelevantPath;
    }
}
