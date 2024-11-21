<?php

namespace ExpImpManagement\ExportersManagement\Responders;

use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\Jobs\HugeDataExporterJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\LazyCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobDispatcherJSONResponder  extends Responder
{
    //protected string $exporterClass = ""; 
    protected Exporter $exporter;
    /**
     * @param Exporter $exporter
     * @return $this
     * @throws Exception
     */
    // public function setExporterClass(Exporter $exporter): self
    // {
    //     $this->exporterClass = get_class($exporter); 
    //     return $this;
    // }
    public function __construct(Exporter $exporter)
    {
        $this->setExporter($exporter);
    }
    public function setExporter(Exporter $exporter): self
    {
        $this->exporter = $exporter;
        return $this;
    }
    protected function dispatchJob(HugeDataExporterJob $job)
    {
        dispatch($job);
    }

    // protected function setHugeDataExporterJobProps(HugeDataExporterJob $job)
    // {
    //     $job->setDataCollection($this->DataCollection);
    // }
    /**
     * @return HugeDataExporterJob
     * @throws Exception
     */
    protected function initHugeDataExporterJob() : HugeDataExporterJob
    {  
        // if(!$this->exporterClass){throw new Exception("There Is No Exporter Class Given To Job Object");}
        // if(!$this->exporter){throw new Exception("There Is No Exporter Passed To Job Object");}
        return  new HugeDataExporterJob( $this->exporter
            //$this->exporterClass
        // , request()
        );
    }

    protected function getHugeDataExporterJob() : HugeDataExporterJob
    {
        $job = $this->initHugeDataExporterJob();
    //    $this->setHugeDataExporterJobProps($job);
        return $job;
    }
    /**
     * @return BinaryFileResponse|StreamedResponse|JsonResponse | string
     */
    public function respond():BinaryFileResponse | StreamedResponse | JsonResponse | string
    {
        $job = $this->getHugeDataExporterJob();
       $this->dispatchJob($job);
        return Response::success([] , ["The Needed Data Is In Large Size , You Will Receive The Needed Data Files On Your Email !"]);
    }

}
