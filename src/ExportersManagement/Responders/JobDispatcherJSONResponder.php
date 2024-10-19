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
    protected string $exporterClass = "";
    protected ?HugeDataExporterJob $job = null;

    /**
     * @return $this
     * @throws Exception
     */
    protected function initJob() : self
    {
        if($this->job){return $this;}
        if(!$this->exporterClass){throw new Exception("There Is No Exporter Class Given To Job Object");}
        $this->job = new HugeDataExporterJob($this->exporterClass , request());
        return $this;
    }

    /**
     * @param Collection|LazyCollection|null $collection
     * @return $this
     * @throws  Exception
     */
    public function setDataCollection(Collection | LazyCollection | null $collection) : self
    {
        $this->initJob();
        $this->job->setDataCollection($collection);
        return $this;
    }

    /**
     * @param Exporter $exporter
     * @return $this
     * @throws Exception
     */
    public function setExporterClass(Exporter $exporter): self
    {
        $this->exporterClass = get_class($exporter);
        $this->initJob();
        return $this;
    }

    /**
     * @return BinaryFileResponse|StreamedResponse|JsonResponse | string
     */
    public function respond():BinaryFileResponse | StreamedResponse | JsonResponse | string
    {
        dispatch($this->job);
        return Response::success([] , ["The Needed Data Is In Large Size , You Will Receive The Needed Data Files On Your Email !"]);
    }

}
