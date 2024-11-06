<?php

namespace ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\Traits;

use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use Exception;

trait DataArrayMappingMethodsTrait
{ 
    /** * @var callable $FinalDataArrayMappingFun */
    protected  $FinalDataArrayMappingFun = null;

    /**
     * @param callable|null $FinalDataArrayMappingFun
     * @return DataArrayMappingMethodsTrait|DataArrayProcessor
     */
    public function setFinalDataArrayMappingFun(?callable $FinalDataArrayMappingFun): self
    {
        $this->FinalDataArrayMappingFun = $FinalDataArrayMappingFun;
        return $this;
    }

    /**
     * @param array $rows
     * @return array
     * @throws JsonException
     */
    protected function callMappingFunOnRowsArray(array $rows) : array
    {
        if(!$this->FinalDataArrayMappingFun){return $rows;}
        $this->checkMappingFunValidity($rows[0]);

        //If No Exception Is Thrown .... The Mapping Function Will Be Applied On The Final Data Array

        $result = [];
        foreach ($rows as $row)
        {
            $result[] = $this->callMappingFunOnSingleRowArray($row);
        }
        return $result;
    }

    /**
     * @param array $testRow
     * @return bool
     * @throws  Exception
     */
    protected function checkMappingFunValidity(array $testRow) : bool
    {
        $callbackResult = $this->callMappingFunOnSingleRowArray($testRow);
        if(!$callbackResult || !is_array($callbackResult)){throw new  Exception("The Given Callback Function Doesn't Return The Expected Array Typed Value") ;}
        return true;
    }

    /**
     * @param array $row
     * @return mixed
     */
    protected function callMappingFunOnSingleRowArray(array $row) : mixed
    {
        return call_user_func($this->FinalDataArrayMappingFun , $row);
    }


}
