<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories;

use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;

abstract class CSVImportableFileFormatFactory 
               extends ImportableFileFormatFactory 
               implements WithStrictNullComparison, WithEvents, WithHeadings, WithColumnWidths, WithStyles
               //need to add methods in this class
{
 
    protected string $fileName;
    protected ?string $writerType = null;
    protected array $headers = [];

    public function __construct(string $fileName, string $writerType = null, array $headers = [])
    { 
        $this->fileName = $fileName;
        $this->writerType = $writerType ?? "Csv";
        $this->headers = $headers;
    }

    protected function initPixelExcelFormatFactoryLib() : PixelExcelFormatFactoryLib
    {
        return  app()->make(PixelExcelFormatFactoryLib::class);   
    }

    public function downloadFormat()
    {
        return $this->initPixelExcelFormatFactoryLib()->download($this , $this->fileName , $this->writerType , $this->headers );
    }

}