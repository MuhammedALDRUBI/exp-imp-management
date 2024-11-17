<?php

namespace ExpImpManagement\DataFilesInfoManagers\ImportingDataFilesInfoManagers;

use ExpImpManagement\DataFilesInfoManagers\DataFilesInfoManager;

class ImportingRejectedDataFilesInfoManager extends DataFilesInfoManager
{

    public const ValidityIntervalDayCount  = 7;


    /**
     * Must be handled later for multi tenancy application
     * it is wrong for multitenancy app ... think about storing in db table or adding tenant key as a key in InfoData array
     * after checking if app is multi tenancy app from PixelApp package config
     */
    protected function getDataFilesInfoPath(): string
    {
        return storage_path("app/ImportingRejectedDataFilesInfo.json");
    }

    /**
     * @param string $fileName
     * @param string $fileRealPath
     * @param string $fileRelevantPath
     * @param int $timestamp_expiration
     * @return $this
     */
    public function addNewFileInfo(string $fileName, string $fileRealPath, string $fileRelevantPath  , int $timestamp_expiration = -1): self
    {
        if($timestamp_expiration < 0){$timestamp_expiration = now()->addDays($this::ValidityIntervalDayCount)->getTimestamp() ;}
        $this->InfoData[$fileName] = [
            "fileRealPath" => $fileRealPath ,
            "fileRelevantPath" => $fileRelevantPath   ,
            "timestamp_expiration" => $timestamp_expiration
        ];
        return $this;
    }

    public function getFileName(string $fileRealOrRelevantPath) : string
    {
        foreach ($this->InfoData as $fileName => $fileInfo)
        {
            if(
                $fileInfo["fileRelevantPath"] === $fileRealOrRelevantPath
                ||
                $fileInfo["fileRealPath"] === $fileRealOrRelevantPath
            )
            {
                return $fileName ;
            }
        }
        return "";
    }
    public function getFileRealPath(string $fileName) : string
    {
        return isset($this->InfoData[$fileName]) ? $this->InfoData[$fileName]["fileRealPath"] : "";
    }

    public function getFileRelevantPath(string $fileName) : string
    {
        return isset($this->InfoData[$fileName]) ? $this->InfoData[$fileName]["fileRelevantPath"] : "";
    }

    public function getExpiredFileRelevantPaths(): array
    {
        return array_map(function($fileInfo){

            if($this->IsFileExpired($fileInfo["timestamp_expiration"]))
            {
                return $fileInfo["fileRelevantPath"];
            }
        } , $this->InfoData);
    }

    public function getExpiredFileRealPaths(): array
    {
        return array_map(function($fileInfo){

            if($this->IsFileExpired($fileInfo["timestamp_expiration"]))
            {
                return $fileInfo["fileRealPath"];
            }
        } , $this->InfoData);
    }


    public function getExpiredFileNamesWithRelevantPath(): array
    {
        $files = [];
        foreach ($this->InfoData as $fileName => $fileInfo)
        {
            if($this->IsFileExpired($fileInfo["timestamp_expiration"]))
            {
                $files[] = [ "fileName" => $fileName ,  "fileRelevantPath" => $fileInfo["fileRelevantPath"] ];
            }
        }
        return $files;
    }

}
