<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;
 
use Throwable;

trait DataImportHookExtension
{

    protected function failedModelImportingTransactrion(array $row , Throwable $e) : void
    {
        $this->addRejectedRowToManuallyChanging($row); 
        parent::failedModelImportingTransactrion($row , $e);
    }
     
    protected function singleRowValidationFailed(array $modelData , Throwable $e) : void
    {
        $this->addRejectedRowToManuallyChanging($modelData); 
        parent::singleRowValidationFailed($modelData , $e);
    }
     
}