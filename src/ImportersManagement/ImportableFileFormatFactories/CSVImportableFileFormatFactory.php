<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponent;
use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class CSVImportableFileFormatFactory 
               extends ImportableFileFormatFactory 
               implements WithStrictNullComparison, WithEvents, WithHeadings, WithColumnWidths, WithStyles
{
 
    protected string $fileName;
    protected  string $writerType = "Csv";
    protected array $headers = [];
    protected array $validColumnFormatInfoCompoenents = [];
    protected int $firstRowHeight = 30;
    protected int $firstRowWidth = 180;

    abstract protected function getColumnFormatInfoCompoenents() : array;

    public function __construct(string $fileName, array $headers = [])
    { 
        $this->fileName = $this->getValidFileName($fileName) ;
        $this->headers = $headers;
        $this->setValidColumnFormatInfoCompoenents();
    }
    protected function getValidFileName(string $fileName) : string
    {
        $nameParts = explode("." , $fileName);
        return $nameParts[0] . ".csv";
    }

    protected function initPixelExcelFormatFactoryLib() : PixelExcelFormatFactoryLib
    {
        return  app()->make(PixelExcelFormatFactoryLib::class);   
    }

    public function downloadFormat()
    {
        return $this->initPixelExcelFormatFactoryLib()->download($this , $this->fileName , $this->writerType , $this->headers );
    }
    
    protected function setValidColumnFormatInfoCompoenents() : void
    {
        array_filter(
                $this->getColumnFormatInfoCompoenents() ,
                function($component)
                {
                    return $component instanceof CSVFormatColumnInfoComponent;
                });
    }

    public function columnWidths(): array
    {
        $columnsWidth = [];
        foreach($this->validColumnFormatInfoCompoenents as $component)
        {
            if($width = $component->getWidth())
            {
                $columnsWidth[ $component->getColumnCharSymbol() ] = $width;
            }
        }
        return $columnsWidth;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function headings(): array
    {
        return array_map(function($component)
               {
                    return $component->getColumnHeaderName();
               } , $this->validColumnFormatInfoCompoenents);
        
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {

                foreach($this->validColumnFormatInfoCompoenents as $component)
                {
                    $this->setDropDownColumnValidation($component, $event);
                } 

                $this->setFirstRowHeight($event);
                $this->setFirstRowWidth($event);
            },
        ];
    }

    public function setDropDownColumnValidation(CSVFormatColumnInfoComponent $columnComponent, AfterSheet $event)
    {
        //A1048576 is Excel's maximum row range.
        $rowRange = $columnComponent->getColumnCharSymbol() ."1:A1048576" ;
        $sheet = $event->sheet->getDelegate();

        $dataValidation = new DataValidation();
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setShowDropDown(true);
        $dataValidation->setErrorTitle('Input error');


        if($dataType = $columnComponent->getDataType())
        {
            $dataValidation->setType( $dataType );
            $dataValidation->setError('Value is not ' . $dataType  . " typed value !");
            $dataValidation->setPromptTitle('Set a valid ' . $dataType . " typed value ");
            $dataValidation->setPrompt('Please set a valid ' . $dataType . " typed value ");
        }

        if($dataType 
           &&
           $dataType == DataValidation::TYPE_LIST 
           &&
           $options =  $columnComponent->getValidValues()
        )
        { 
            $dataValidation->setError('Value is not in list.');
            $dataValidation->setPromptTitle('Pick from list');
            $dataValidation->setPrompt('Please pick a value from the drop-down list.');
            $dataValidation->setFormula1(sprintf('"%s"', implode(',', $options)));
        }
        $sheet->setDataValidation($rowRange , $dataValidation);
          
        $event->sheet->getColumnDimension($columnComponent->getColumnCharSymbol())->setAutoSize(true);
         
    }
    


    // public function setDropDownColumnValidation($column, $options, $row_count, $column_count, AfterSheet $event)
    // {
    //     // set dropdown list for first data row
    //     $validation = $event->sheet->getCell("{$column}2")->getDataValidation();
    //     $validation->setType(DataValidation::TYPE_LIST);
    //     $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
    //     $validation->setAllowBlank(false);
    //     $validation->setShowInputMessage(true);
    //     $validation->setShowErrorMessage(true);
    //     $validation->setShowDropDown(true);
    //     $validation->setErrorTitle('Input error');
    //     $validation->setError('Value is not in list.');
    //     $validation->setPromptTitle('Pick from list');
    //     $validation->setPrompt('Please pick a value from the drop-down list.');
    //     $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

    //     // clone validation to remaining rows
    //     for ($i = 3; $i <= $row_count; $i++) {
    //         $event->sheet->getCell("{$column}{$i}")->setDataValidation(clone $validation);
    //     }

    //     // set columns to autosize
    //     for ($i = 1; $i <= $column_count; $i++) {
    //         $column = Coordinate::stringFromColumnIndex($i);
    //         $event->sheet->getColumnDimension($column)->setAutoSize(true);
    //     }
    // }

    protected function setFirstRowHeight(AfterSheet $event) : void
    {
        $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight( $this->firstRowHeight );
    }
    
    protected function setFirstRowWidth(AfterSheet $event) : void
    {
        $event->sheet->getDelegate()->getColumnDimension('A')->setWidth($this->firstRowWidth );
    }
}