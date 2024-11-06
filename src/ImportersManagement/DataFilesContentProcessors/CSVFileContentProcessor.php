<?php

namespace ExpImpManagement\ImportersManagement\DataFilesContentProcessors;

use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException; 

class CSVFileContentProcessor extends DataFileContentProcessor
{
    protected function initPixelExcelExpImpLib() : PixelExcelExpImpLib
    {
        return app()->make(PixelExcelExpImpLib::class);
    }
    /**
     * @return array
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function getData(): array
    {
        return $this->initPixelExcelExpImpLib()->import( $this->filePathToProcess )->toArray();
    }
}
