<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;
 
use Throwable;

trait DataImportHookExtension
{

    protected function failedDataRowImportingTransactrion(array $row , Throwable $e) : void
    {
        $this->addRejectedRowToManuallyChanging($row); 
        parent::failedDataRowImportingTransactrion($row , $e);
    }
     
    protected function singleDataRowValidationFailed(array $modelData , Throwable $e) : void
    {
        $this->addRejectedRowToManuallyChanging($modelData); 
        parent::singleDataRowValidationFailed($modelData , $e);
    }
     
}