<?php

namespace ExpImpManagement\ImportersManagement\Jobs\Traits;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Jobs\DataImporterJob;
use ExpImpManagement\ImportersManagement\Notifications\SuccessfulImportingNotification;
use Exception;

trait JobHandlingMethods
{

    private ?Importer $importer = null;

    /**
     * @throws Exception
     */
    private function setImporterProps() : Importer
    {
        return $this->importer->setUploadedFileStorageRelevantPath($this->importedDataFileStoragePath);
    }

    /**
     * @return Importer
     * @throws Exception
     */
    private function initImporter() : Importer
    {
        if(!$this->importer)
        {
            $this->importer = new $this->importerClass;
        }
        return $this->setImporterProps();
    }

    /**
     * @return DataImporterJob
     */
    protected function SuccessfullyImportingDataNotifier( ) : DataImporterJob
    {
        $this->notifiable->notify(new SuccessfulImportingNotification());
        return $this;
    }
}
