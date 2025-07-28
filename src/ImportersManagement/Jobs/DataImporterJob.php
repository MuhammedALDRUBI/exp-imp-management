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
use Illuminate\Notifications\Notification;
use PixelApp\Models\UsersModule\PixelUser;

class DataImporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Importer $importer = null;
    private PixelUser $notifiable;

    
    /**
     * @param string $importerClass
     * @throws JsonException
     */
    public function __construct(Importer $importer )
    {
        $this->setImporter($importer);
    }

    public static function firstTimeInit(Importer $importer ) : self
    {
        return (new static( $importer ))->setNotifiable();
    }

    private function setImporter(Importer $importer) : DataImporterJob
    { 
        $this->importer  = $importer ; 
        return $this;
    }

    public function setNotifiable() : self
    {
        $loggedUser = auth("api")->user();

        if(!$loggedUser instanceof PixelUser)
        {
            throw new Exception("The logged user object is not child type of PixelUser .... Make sure that logged user type class is inhertir of PixelUser class !");
        }

        $this->notifiable =  $loggedUser;

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
