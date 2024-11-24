<?php

namespace ExpImpManagement\ImportersManagement\Jobs;


use Exception;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notification;

class DataImporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private ?Importer $importer = null;
    private Authenticatable $notifiable;

    
    /**
     * @param string $importerClass
     * @throws JsonException
     */
    public function __construct(Importer $importer )
    {
        $this->setImporter($importer)->setNotifiable();
    }

    private function setImporter(Importer $importer) : DataImporterJob
    { 
        $this->importer  = $importer ; 
        return $this;
    }

    private function setNotifiable() : self
    {
        $this->notifiable = auth("api")->user();
        return $this;
    } 
    
    protected function getConvinientNotification() :Notification
    {
        return $this->importer->getConvinientNotification();
    }

    /**
     * @return DataImporterJob
     */
    protected function SuccessfullyImportingDataNotifier( ) : DataImporterJob
    {
        $this->notifiable->notify( $this->getConvinientNotification() );
        return $this;
    }


    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function handle(Request $request)
    {  
        $this->importer->importingJobFun();
        $this->SuccessfullyImportingDataNotifier();
    }
}
