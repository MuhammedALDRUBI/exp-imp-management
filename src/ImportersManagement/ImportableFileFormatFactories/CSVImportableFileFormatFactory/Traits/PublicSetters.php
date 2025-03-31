<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponent;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

trait PublicSetters
{
    public function setHeaders(array $headers = []) : self
    {
        $this->headers = $headers;
        return $this;
    }

    public function setFileName(string $fileName) : self
    {
        $this->fileName = $this->getValidFileName($fileName) ;
        return $this;
    }

    public function setDataFileToManuallyChange(array | Collection $data ) : self
    { 
        if(is_array($data))
        {
            $data = collect($data);
        }
        
        $this->formatDataCollection = $this->getValidSortedData($data);

        return $this;
    }
    
    public function setColumnValidation(CSVFormatColumnInfoComponent $columnComponent, AfterSheet $event)
    {
        if($cellValidationSetter = $columnComponent->getCellDataValidation())
        {
            $charSymbol = $columnComponent->getColumnCharSymbol();
            //1048576 is Excel's maximum row range.
            $rowRange =  $charSymbol ."1:" . $charSymbol . "1048576" ;

            $sheet = $event->sheet->getDelegate();

            $dataValidation = new DataValidation();

            $cellValidationSetter->setCellDataValidation($dataValidation);

            $sheet->setDataValidation($rowRange , $dataValidation);
        }
            
    }
    

}