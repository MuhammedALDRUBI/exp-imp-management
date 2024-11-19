<?php

namespace ExpImpManagement\ImportersManagement\Jobs\Traits;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Jobs\DataImporterJob;
use Exception; 
use Illuminate\Contracts\Auth\Authenticatable;

trait JobSerializingMethods
{
    private string $importerClass;
    protected ?string $importedDataFileTempPath = null;  
    private Authenticatable $notifiable;

    /**
     * @param string $importedDataFileTempPath
     * @return DataImporterJob
     */
    public function setImportedDataFileTempPath(string $importedDataFileTempPath): DataImporterJob
    {
        $this->importedDataFileTempPath = $importedDataFileTempPath;
        return $this;
    }
  
    /**
     * @param string $importerClass
     * @return DataImporterJob
     * @throws Exception
     */
    private function setImporterClass(string $importerClass) : DataImporterJob
    {
        if(!is_subclass_of($importerClass , Importer::class))
        {
            throw new Exception("The Given Importer Class Is Not Valid Importer Class !");
        }
        
        $this->importerClass = $importerClass;

        return $this;
    }


    private function setNotifiable() : self
    {
        $this->notifiable = auth("api")->user();
        return $this;
    }
}
