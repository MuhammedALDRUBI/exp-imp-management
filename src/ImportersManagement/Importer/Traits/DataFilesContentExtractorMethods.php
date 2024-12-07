<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\DataFilesContentExtractor;
use Illuminate\Support\Collection;

trait DataFilesContentExtractorMethods
{

    protected ?DataFilesContentExtractor $dataFileContentExtractor = null; 
    
    protected function setDataFilesContentExtractorProps() : DataFilesContentExtractor
    { 
        return $this->dataFileContentExtractor->setFilesProcessor($this->filesProcessor)
                                              ->setFilePathToProcess($this->uploadedFileTempRealPath); 
    }

    protected function initDataFilesContentExtractor() : DataFilesContentExtractor
    {
        if(!$this->dataFileContentExtractor)
        {
            $this->dataFileContentExtractor = $this->getDataFilesContentExtractor();
        }
        return $this->setDataFilesContentExtractorProps();
    }
    
    protected function extraactFileDataToImport() : Collection
    {
        return $this->initDataFilesContentExtractor()->getData();
    }
  
}