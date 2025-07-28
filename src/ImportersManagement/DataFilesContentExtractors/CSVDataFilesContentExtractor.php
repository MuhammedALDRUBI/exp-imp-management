<?php

namespace ExpImpManagement\ImportersManagement\DataFilesContentExtractors;

use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use Illuminate\Support\Collection;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException; 

class CSVDataFilesContentExtractor extends DataFilesContentExtractor
{
    protected function initPixelExcelExpImpLib() : PixelExcelExpImpLib
    {
        return app()->make(PixelExcelExpImpLib::class);
    }
    
    /**
     * @return Collection  
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function getData(): Collection 
    {
        return $this->initPixelExcelExpImpLib()->importDataFile( $this->filePathToProcess ) ;
    }
}
