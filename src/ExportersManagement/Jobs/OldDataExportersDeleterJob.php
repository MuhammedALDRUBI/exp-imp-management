<?php

namespace ExpImpManagement\ExportersManagement\Jobs;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use CustomFileSystem\CustomFileDeleter;
use CustomFileSystem\S3CustomFileSystem\CustomFileDeleter\S3CustomFileDeleter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


/**
 * Not finished
 * need to handle it later for multi tenancy app
 */

class OldDataExportersDeleterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
   protected ?CustomFileDeleter $customFileDeleter = null;
   protected ?ExportedDataFilesInfoManager $dataFilesInfoManager = null;

   protected array $successfullyDeletedFilesNames = [];
    /**
     * @return $this
     */
    protected function informDataFilesInfoManager() : self
    {
        $this->dataFilesInfoManager->removeExpiredFilesInfo($this->successfullyDeletedFilesNames)
                                   ->SaveChanges();
        return $this;
    }
    /**
     * @return $this 
     */
    protected function DeleteMustDeletedFiles() : self
    {

        foreach ($this->dataFilesInfoManager->getExpiredFileNamesWithRelevantPath() as $fileInfo)
        {
            if($this->customFileDeleter->deleteFileIfExists( $fileInfo["fileRelevantPath"] ) )
            {
                $this->successfullyDeletedFilesNames[] = $fileInfo["fileName"];
            }
        }
        return $this->informDataFilesInfoManager();
    }


    /**
     * @return $this
     */
    public function initDataFilesInfoManager(): self
    {
        if(!$this->dataFilesInfoManager)
        {
            $this->dataFilesInfoManager = new ExportedDataFilesInfoManager();   
        }
        return $this;
    }

    protected function initCustomFileDeleter() : self
    {
        if(!$this->customFileDeleter)
        {
            $this->customFileDeleter = new S3CustomFileDeleter();
        }
        return $this;
    }
 
    /**
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $this->initCustomFileDeleter()->initDataFilesInfoManager()->DeleteMustDeletedFiles();
    }
}
