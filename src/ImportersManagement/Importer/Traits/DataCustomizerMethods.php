<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use CRUDServices\DatabaseManagers\MySqlDatabaseManager;
use ExpImpManagement\ImportersManagement\Importer\Importer; 
use Exception; 
use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\DataFilesContentExtractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection; 
use Throwable;

trait DataCustomizerMethods
{
    protected array $currentModelFillables = [];
    protected array $currentDataRow = []; 

    protected function setModelClass(string $modelClass) : self
    { 
        if(! is_subclass_of($modelClass , Model::class))
        {
            throw new Exception("Invalid model class is provided !");
        }
        $this->ModelClass = $modelClass;
        return $this;
    }
     
    protected function setCurrentModelFillables() : self
    {
        $this->currentModelFillables = $this->getCurrentModelFillableValues($this->currentDataRow);
        return $this;
    }

    protected function setTheCurrentDataRow(array $row) : void
    {
        $this->currentDataRow = $row;
    } 

    // protected function setModelDateColumns(array $columns) : array
    // {
    //     if($this instanceof CareAboutDateTruth)
    //     {
    //         return array_merge( $columns , $this->getDateColumns());
    //     }
    //     return $columns;
    // }

    // protected function getModelDBTable() : string
    // {
    //     return $this->initNewModel()->getTable();
    // }
 
    protected function initNewModel() : Model
    {
        return app()->make($this->ModelClass);
    }
      
    protected function importCurrentModel() : Model
    { 
        $Model = $this->initNewModel();

        $Model->forceFill($this->currentModelFillables);  

        $Model->save(); 

        return $Model;
    }
 
 
    protected function startDataRowImporitng() : void
    { 
        try {

            //starting a new database transaction
            $this->startDataRowImportingDBTransaction();

            //importing mode using $this->currentModelFillables (the single model data will validated in importCurrentModel method)
            $model = $this->importCurrentModel();    

            //an abstract method to allow child handle the relationships based on its type 
            $this->handleModelRelationships($model);

            //commiting database transaction if no exception is thrown
            $this->successfulDataRowImportingTransaction();

        }catch (Throwable $e)
        {
            //on any failing this data importing hook method will be called to allow child class to deal with failing
            $this->failedDataRowImportingTransactrion( $this->currentDataRow , $e);
        }
        
    }

    /**
     * to allow the chuild class to add more validation functionality to be handled in validation try , catch part
     */
    protected function processSingleDataRowValidation() : bool
    {
        $this->validateFileSingleDataRow($this->currentDataRow); // validate row by rules() method found in RequestForm Class
        $this->validateSingleModelData($this->currentModelFillables);
        return true; //if no exception is thrown true will be return
    }

    protected function FailingHandlerDataRowValidation() : bool
    {
        try
        {  
            return $this->processSingleDataRowValidation();

        }catch(Throwable $e)
        {

            $this->singleDataRowValidationFailed($this->currentDataRow , $e);
            return false; // to stop Data row importing execution after handling validation exception in 

        }
    }

    protected function isItRequestedToTuncateTable() : bool
    {
        return $this->truncateTableBeforeImproting;
    }

    protected function truncateByCRUDDatabaseManager(string $tableName) : void
    {
        MySqlDatabaseManager::truncateDBTable($tableName);
    }
    
    protected function truncateTableIfRequested() : void
    {
        if($this->isItRequestedToTuncateTable())
        {
            $Model = $this->initNewModel();
            $tableName = $Model->getTable();
            $this->truncateByCRUDDatabaseManager($tableName);
        }
    }

    //this method allow the child class to control the conditions of any data row importing
    protected function checkDataRowBeforeImporting() : bool
    { 
        return $this->FailingHandlerDataRowValidation()
               &&
               !empty( $this->currentModelFillables );
    }
 
    protected function prepareDataRowForImporting(array $row) : void
    {
        $this->setTheCurrentDataRow($row);
        $this->setCurrentModelFillables();
    }

    /**
     * @param array $row 
     * @throws Exception
     */
    protected function handleDataRowImporting(array $row) : void
    {  
        $this->prepareDataRowForImporting($row);

        if( $this->checkDataRowBeforeImporting() )
        { 
            $this->truncateTableIfRequested();
            $this->startDataRowImporitng();
        } 
    }

    protected function importDataRows() : void
    {
        foreach ($this->ImportedDataArray as $row)
        {
            $this->handleDataRowImporting($row);
        }
    }
  
    /**
     * @throws Exception
     */
    protected function importData() : Importer
    {   
        $this->prepareValidationManagerForDataValidation();
        $this->importDataRows();
        return $this;
    }

    protected function setTableTruncatingStatus(?bool $status = null) : self
    {
        if($status === null)
        {
            $status =  $this->getTableTruncatingRequestingStatus();
        }

        $this->truncateTableBeforeImproting = $status;
        return $this;
    }    

 
}
