<?php

namespace ExpImpManagement\ExportersManagement\Jobs;

use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\Notifications\ExportedDataFilesNotifier;
use PixelApp\Models\UsersModule\PixelUser;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HugeDataExporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PixelUser $notifiable; 
    private array $RequestQueryStringArray = [];
    private array $RequestPostData = []; 
 
    private Exporter $exporter;

    /**
     * @param string $ExporterClass
     * @param Request $request
     * @throws Exception
     */
    public function __construct( )
    {
        
    }

    public static function firstTimeInit(Exporter $Exporter ) : self
    {
        $instance = (new static());
        $instance->setExporter($Exporter)->keepRequestParams()->setNotifiable();
        return $instance;
    }
 

    public function keepRequestParams() :self
    { 
        $request = request();
        $this->RequestQueryStringArray = $request->query->all();
        $this->RequestPostData = $request->all();
        return $this;
    }

    /**
     * @param string $ExporterClass
     * @return $this
     * @throws Exception
     */
    public function setExporter(Exporter $Exporter) : self
    { 
        $this->exporter = $Exporter;
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
 

    protected function NotifyExportedData(string $ExportedDataDownloadingPath) : self
    {
        $this->notifiable->notify(new ExportedDataFilesNotifier($ExportedDataDownloadingPath));
        return $this;
    }

    private function syncRequestSavedParameters(Request $request) : void
    {
        $request->merge([ ...$this->RequestPostData , ...$this->RequestQueryStringArray] );
        $this->exporter->useRequest( $request ); 
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function handle(Request $request) : void
    {  
        $this->syncRequestSavedParameters($request);

        $ExportedDataDownloadingPath = $this->exporter->exportingJobFun();  

        $this->NotifyExportedData($ExportedDataDownloadingPath);
    }
}
