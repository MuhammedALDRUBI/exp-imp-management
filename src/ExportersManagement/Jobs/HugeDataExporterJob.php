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
    private Request $Request;
    private array $RequestQueryStringArray = [];
    private array $RequestPostData = [];
    private Collection | LazyCollection | null $DataCollection = null;

    private string $ExporterClass;
    private Exporter $exporter;

    /**
     * @param string $ExporterClass
     * @param Request $request
     * @throws Exception
     */
    public function __construct(string $ExporterClass , Request $request )
    {
        $this->setExporterClass($ExporterClass)->keepRequestParams($request)->setNotifiable();
    }

    public function setDataCollection(Collection | LazyCollection | null $collection) : self
    {
        $this->DataCollection = $collection;
        return $this;
    }

    private function updateRequest(Request $request) : Request
    {
        return $request->merge([ ...$this->RequestPostData , ...$this->RequestQueryStringArray] );
    }

    protected function keepRequestParams(Request $request) :self
    { 
        $this->RequestQueryStringArray = $request->query->all();
        $this->RequestPostData = $request->all();
        return $this;
    }
    /**
     * @param string $ExporterClass
     * @return $this
     * @throws Exception
     */
    private function setExporterClass(string $ExporterClass) : self
    {
        if(!is_subclass_of($ExporterClass , Exporter::class))
        {
            throw new Exception("The Given Exporter Class Is Not Valid Exporter Class !");
        } 
        $this->ExporterClass = $ExporterClass ;

        return $this;
    }

    private function setNotifiable() : self
    {
        $this->notifiable =  auth("api")->user();
        return $this;
    }

    private function setExporter() : self
    {
        $this->exporter = new $this->ExporterClass;
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
        $this->setExporter();
        $this->exporter->useRequest( $this->updateRequest($request) );
        $this->exporter->useDataCollection($this->DataCollection);
        $ExportedDataDownloadingPath = $this->exporter->exportingJobFun(); 
        $this->NotifyExportedData($ExportedDataDownloadingPath);
    }
}
