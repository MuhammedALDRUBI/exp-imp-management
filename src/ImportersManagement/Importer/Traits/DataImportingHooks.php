<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use Illuminate\Support\Facades\DB;
use Throwable;

trait DataImportingHooks
{
 
    /**
     * @todo later
     */
    protected function singleDataRowValidationFailed(array $row , Throwable $e) : void
    {
        /**
         * Need to set a behavior for failing validation
         */
        return ;
    }

    protected function successfulDataRowImportingTransaction() : void
    {
        DB::commit();  
    }

    protected function failedDataRowImportingTransactrion(array $row , Throwable $e) : void
    {
        /**
         * Need to set a behavior for failing inserting
         */
        DB::rollBack(); 
    }
    protected function startDataRowImportingDBTransaction() : void
    {
        DB::beginTransaction();
    }

}