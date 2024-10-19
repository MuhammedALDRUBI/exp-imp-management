<?php


namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter;

use ExpImpManagement\ExportersManagement\Exporter\ExportersMainTypes\ImportableDataExporter;
use ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Responders\CSVStreamingResponder;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\ImportingDataArrayProcessors\ParentRelationshipsContainerArrayProcessor;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;
use Exception;
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;

abstract class CSVExporter extends ImportableDataExporter
{
    protected function getFinalDataArrayProcessor(): DataArrayProcessor
    {
        return new ParentRelationshipsContainerArrayProcessor();
    }

    protected function getStreamingResponder(): StreamingResponder
    {
        return new CSVStreamingResponder();
    }

    protected function getDataFileExtension() : string
    {
        return "csv";
    }

    /**
     * @return string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws Exception
     */
    protected function setDataFileToExportedFilesProcessor() : string
    {
        return $this->filesProcessor->HandleTempFileToCopy(
                    (new FastExcel($this->DataToExport))->export( $this->fileFullName ),
                    $this->fileFullName
                )->copyToTempPath();
    }

}
