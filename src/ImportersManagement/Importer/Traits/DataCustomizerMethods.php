<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Interfaces\CareAboutDateTruth;
use Exception; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

trait DataCustomizerMethods
{

    protected ?DataFileContentProcessor $dataFileContentProcessor = null;
    protected array $ModelDesiredColumns = [];

    protected function setModelClass(string $modelClass) : self
    { 
        if(! is_subclass_of($modelClass , Model::class))
        {
            throw new Exception("Invald model class is provided !");
        }
        $this->ModelClass = $modelClass;
        return $this;
    }

    protected function setDataFileContentProcessorProps() : DataFileContentProcessor
    { 
        return $this->dataFileContentProcessor->setFilesProcessor($this->filesProcessor)
                                              ->setFilePathToProcess($this->uploadedFileTempRealPath); 
    }

    protected function initDataFileContentProcessor() : DataFileContentProcessor
    {
        if(!$this->dataFileContentProcessor)
        {
            $this->dataFileContentProcessor = $this->getDataFileContentProcessor();
        }
        return $this->setDataFileContentProcessorProps();
    }

    protected function getDataToImport() : array
    {
        return $this->initDataFileContentProcessor()->getData();
    }
  
    protected function setModelDateColumns(array $columns) : array
    {
        if($this instanceof CareAboutDateTruth)
        {
            return array_merge( $columns , $this->getDateColumns());
        }
        return $columns;
    }

    protected function getModelDBTable() : string
    {
        return $this->initNewModel()->getTable();
    }
    /**
     * Override It When It Is Needed In Child Class
     * @return array
     */
    protected function getModelDesiredColumns() : array
    {
        return Schema::getColumnListing( $this->getModelDBTable()  );
    }

    protected function setModelDesiredColumns() : self
    {
        $columns = $this->getModelDesiredColumns();
        $this->ModelDesiredColumns = $this->setModelDateColumns($columns);
        return $this;
    }

    protected function initNewModel() : Model
    {
        return app()->make($this->ModelClass);
    }
    
    protected function importModel(array $row) : void
    {
        try {

            $this->startModelImportingDBTransaction();

            $Model = $this->initNewModel();

            $Model->forceFill($row);  

            $Model->save(); 

            $this->successModelfulImportingTransaction();

        }catch (Throwable $e)
        {
            $this->failedModelImportingTransactrion( $row , $e);
        }
    }

    protected function getModelDesiredColumnValues(array $dataRow) : array
    {
        $columnsValues = [] ;

        foreach ($this->ModelDesiredColumns as $column)
        {
            if(isset($dataRow[$column]))
            {
                $columnsValues[$column] =  $dataRow[$column] ?: null ;
            }
        }
        return $columnsValues;
    }

    protected function processModelRowValidation(array $row) :  bool
    {
        try
        {
            
            return $this->validateSingleModel($row);

        }catch(Throwable $e)
        {
            $this->singleRowValidationFailed($row , $e);
            return false; // to stop importDataRow execution
        }
    }

    /**
     * @param array $row 
     * @throws Exception
     */
    protected function importDataRow(array $row) : void
    {
        if( 
            $this->processModelRowValidation($row)
            &&
            !empty( $fillable = $this->getModelDesiredColumnValues($row) )
          )
        {
            $this->importModel($fillable);
        }
    }

    protected function importDataRows() : void
    {
        foreach ($this->ImportedDataArray as $row)
        {
            $this->importDataRow($row);
        }
    }

    /**
     * @throws Exception
     */
    protected function importData() : Importer
    {   
        $this->setModelDesiredColumns()->importDataRows();
        return $this;
    }
 
}
