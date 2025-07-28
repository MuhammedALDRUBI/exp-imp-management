<?php

namespace ExpImpManagement\PixelAdapters;

use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PixelExcelFormatFactoryLibAdapter extends Excel implements PixelExcelFormatFactoryLib
{

    /**
     * 
     * 
     * Note :
     * Any new adapter must handle the interfaces implemented of the $factory object passed to the libirary
     * Ex :
     * CSVImportableFileFormatFactory uses the PixelExcelFormatFactoryLib interface to create the binded libirary
     * but it is passed all of the format required props by implementing some interfaces found in Laravel Excel package (Maatwebsite\Excel)
     * so any new adapter must handle it while recieving a CSVImportableFileFormatFactory instance (this adapter does that already) 
     * 
     */


    public function downloadFile($export, string $fileName, ?string $writerType = null, array $headers = []) : BinaryFileResponse
    {
        return $this->download($export , $fileName , $writerType , $headers);
    }

    
    /**
     * Must export a file and return its raw contentt without storing or streaming it 
     */
    public function exportFileRawContent($export, string $writerType) : string
    {
        return $this->raw($export , $writerType);
    }


}