<?php

namespace ExpImpManagement\ImportersManagement\Jobs;


use ExpImpManagement\ImportersManagement\Jobs\Traits\JobHandlingMethods;
use ExpImpManagement\ImportersManagement\Jobs\Traits\JobSerializingMethods;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DataImporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //Own Traits
    use JobHandlingMethods , JobSerializingMethods;

    /**
     * @param string $importerClass
     * @throws JsonException
     */
    public function __construct(string $importerClass )
    {
        $this->setImporterClass($importerClass)->setNotifiable();
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function handle(Request $request)
    {
        $this->initImporter()
             ->setImportedDataFileAfterProcessingDeletingStatus($this->ImportedDataFileAfterProcessingDeletingStatus)
             ->importingJobFun();
        $this->SuccessfullyImportingDataNotifier();
    }
}
