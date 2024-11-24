<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use Illuminate\Support\Facades\DB;
use Throwable;

trait DataImportingHooks
{
 
    /**
     * @todo later
     */
    protected function singleRowValidationFailed(array $modelData , Throwable $e) : void
    {
        /**
         * Need to set a behavior for failing validation
         */
        return ;
    }
    protected function successModelfulImportingTransaction() : void
    {
        DB::commit();  
    }

    protected function failedModelImportingTransactrion(array $row , Throwable $e) : void
    {
        /**
         * Need to set a behavior for failing inserting
         */
        DB::rollBack(); 
    }
    protected function startModelImportingDBTransaction() : void
    {
        DB::beginTransaction();
    }

}