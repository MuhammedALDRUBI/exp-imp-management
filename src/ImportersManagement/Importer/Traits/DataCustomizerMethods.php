<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Interfaces\CareAboutDateTruth;
use Exception;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait DataCustomizerMethods
{

    protected ?DataFileContentProcessor $dataFileContentProcessor = null;
    protected array $ModelDesiredColumns = [];

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

    public function getDataToImport() : array
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

    protected function getModelDesiredColumnValues(array $dataRow) : array
    {
        $columnsValues = [] ;

        foreach ($this->ModelDesiredColumns as $column)
        {
            if(array_key_exists($column , $dataRow))
            {
                $columnsValues[$column] = $dataRow[$column];
            }
        }
        return $columnsValues;
    }

    protected function setModelClass() : self
    {
        $modelClass = $this->getModelClass() ;
        if(! is_subclass_of($modelClass , Model::class))
        {
            throw new Exception("Invald model class is provided !");
        }
        $this->ModelClass = $modelClass;
        return $this;
    }

    protected function initNewModel() : Model
    {
        return app()->make($this->ModelClass);
    }
    
    protected function importModel(array $row) : Model
    {
        $Model = $this->initNewModel();
        $Model->forceFill($row);
        $Model->save();
        return $Model; 
    }

    /**
     * @param array $row
     * @return Model | null
     * @throws Exception
     */
    protected function importDataRow(array $row) : Model | null
    {
        /**
         * Need to handle exception notification
         */
        $this->validateSingleModel($row);

        $fillable = $this->getModelDesiredColumnValues($row);
        if(!empty($fillable))
        {
            return $this->importModel($fillable);
        }
         /**
         * Need to handle exception notification
         */
        return null;
    }

    protected function importDataRows(array $row) : void
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
        /**
         * Need to handle exception notification
         */
        $this->validateDataGenerally();
        $this->startImportingDBTransaction();
        $this->setModelDesiredColumns(); 
        return $this;
    }



}
