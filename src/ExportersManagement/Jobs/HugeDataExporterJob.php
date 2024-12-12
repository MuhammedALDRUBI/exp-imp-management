<?php

namespace ExpImpManagement\ExportersManagement\Jobs;

use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\Notifications\ExportedDataFilesNotifier;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class HugeDataExporterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Authenticatable $notifiable; 
    private array $RequestQueryStringArray = [];
    private array $RequestPostData = []; 
 
    private Exporter $exporter;

    /**
     * @param string $ExporterClass
     * @param Request $request
     * @throws Exception
     */
    public function __construct(Exporter $Exporter )
    {
        $this->setExporter($Exporter)->keepRequestParams()->setNotifiable();
    }
 

    private function updateRequest(Request $request) : Request
    {
        return $request->merge([ ...$this->RequestPostData , ...$this->RequestQueryStringArray] );
    }

    protected function keepRequestParams() :self
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
    private function setExporter(Exporter $Exporter) : self
    { 
        $this->exporter = $Exporter;
        return $this;
    }

    private function setNotifiable() : self
    {
        $this->notifiable =  auth("api")->user();
        return $this;
    }
 

    protected function NotifyExportedData(string $ExportedDataDownloadingPath) : self
    {
        $this->notifiable->notify(new ExportedDataFilesNotifier($ExportedDataDownloadingPath));
        return $this;
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function handle(Request $request) : void
    {  
        $this->exporter->useRequest( $this->updateRequest($request) ); 
        $ExportedDataDownloadingPath = $this->exporter->exportingJobFun();  
        $this->NotifyExportedData($ExportedDataDownloadingPath);
    }
}
