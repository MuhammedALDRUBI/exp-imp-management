<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\JSONExporter;

use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\Exporter\ExportersMainTypes\ImportableDataExporter;
use ExpImpManagement\ExportersManagement\ExporterTypes\JSONExporter\Responders\JSONStreamingResponder;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\ImportingDataArrayProcessors\ChildRelationshipsContainerArrayProcessor;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;


abstract class JSONExporter extends ImportableDataExporter
{
    protected function getFinalDataArrayProcessor(): DataArrayProcessor
    {
        return new ChildRelationshipsContainerArrayProcessor();
    }

    protected function getStreamingResponder(): StreamingResponder
    {
        return new JSONStreamingResponder();
    }

    protected function getJsonContent() : string
    {
        return json_encode($this->DataToExport ,JSON_PRETTY_PRINT );
    }

    /**
     * @return Exporter
     */
    protected function setStreamingResponderResponseProps(): Exporter
    {
        $this->responder->setJsonContent( $this->getJsonContent() ) ;
        return $this;
    }

    protected function getDataFileExtension() : string
    {
        return "json";
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function setDataFileToExportedFilesProcessor() : string
    {
        return $this->filesProcessor->HandleTempFileContentToCopy(
                    $this->getJsonContent(),
                    $this->fileFullName
                )->copyToTempPath();
    }

}
