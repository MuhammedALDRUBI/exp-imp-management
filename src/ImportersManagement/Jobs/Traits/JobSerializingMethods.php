<?php

namespace ExpImpManagement\ImportersManagement\Jobs\Traits;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Jobs\DataImporterJob;
use Exception;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait JobSerializingMethods
{
    private string $importerClass;
    protected string $importedDataFileStoragePath = "";
    protected bool $DeleteImportedDataFileAfterProcessing = false;
    private Authenticatable | User $notifiable;

    /**
     * @param string $importedDataFileStoragePath
     * @return DataImporterJob
     */
    public function setImportedDataFileStoragePath(string $importedDataFileStoragePath): DataImporterJob
    {
        $this->importedDataFileStoragePath = $importedDataFileStoragePath;
        return $this;
    }

    /**
     * @param bool $DeleteImportedDataFileAfterProcessing
     * @return DataImporterJob
     */
    public function informToDeleteImportedDataFileAfterProcessing(bool $DeleteImportedDataFileAfterProcessing): DataImporterJob
    {
        $this->DeleteImportedDataFileAfterProcessing = $DeleteImportedDataFileAfterProcessing;
        return $this;
    }

    /**
     * @param string $importerClass
     * @return DataImporterJob
     * @throws Exception
     */
    private function setImporterClass(string $importerClass) : DataImporterJob
    {
        if(!class_exists($importerClass)){throw new Exception("The Given Importer Class Is Not Defined !");}

        $importer = new $importerClass();
        if(!$importer instanceof Importer){throw new Exception("The Given Importer Class Is Not Valid Importer Class !");}
        $this->importerClass = $importerClass ;

        return $this;
    }


    private function setNotifiable() : self
    {

        /**
         * need to get logged user .... not me
         */
        $this->notifiable = User::where("email" , "aldroubim7@gmail.com")->first();
//        $this->notifiable = auth("api")->user();
        return $this;
    }
}
