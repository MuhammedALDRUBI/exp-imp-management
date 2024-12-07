<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\DataProcessors\ImportableDataProcessors\ImportableDataProcessor;
use Illuminate\Support\Collection;

trait ImportableDataProcessorMethods
{
    protected ?ImportableDataProcessor $importableDataProcessor = null;

    public function useImportableDataProcessor(ImportableDataProcessor $importableDataProcessor) : self
    {
        $this->importableDataProcessor = $importableDataProcessor;
        return $this;
    }

    protected function initImportableDataProcesor() : ImportableDataProcessor
    {
        if(!$this->importableDataProcessor)
        {
            $this->importableDataProcessor = $this->getDefaultImportableDataProcessor();
        }
        return $this->importableDataProcessor;
    }

    protected function processFileData(Collection $fileData) : Collection
    {
        return $this->initImportableDataProcesor()->processData($fileData);
    }


}