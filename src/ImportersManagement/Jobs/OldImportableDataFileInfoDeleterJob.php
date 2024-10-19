<?php

namespace ExpImpManagement\ImportersManagement\Jobs;
 
use Exception;
use ExpImpManagement\DataFilesInfoManagers\ImportableDataFilesInfoManager\ImportableDataFilesInfoManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class OldImportableDataFileInfoDeleterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   protected ?ImportableDataFilesInfoManager $dataFilesInfoManager = null;

    /**
     * @return $this
     */
    public function initDataFilesInfoManager(): self
    {
        if($this->dataFilesInfoManager){return $this;}
        $this->dataFilesInfoManager = new ImportableDataFilesInfoManager();
        return $this;
    }

    /**
     * @return void
     */
    protected function DeleteMustDeletedRows() : void
    {
        $this->dataFilesInfoManager->removeExpiredFilesInfo();
    }


    /**
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $this->initDataFilesInfoManager()->DeleteMustDeletedRows();
    }
}
