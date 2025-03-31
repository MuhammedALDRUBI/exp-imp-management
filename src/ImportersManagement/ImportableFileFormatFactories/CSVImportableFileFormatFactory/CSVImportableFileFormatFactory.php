<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits\CSVImportableFileFormatFactorySerilizing;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits\PublicSetters;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits\PublicsGetters;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponent;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ImportableFileFormatFactory;
use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Illuminate\Support\Collection;
use JsonSerializable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class CSVImportableFileFormatFactory 
               extends ImportableFileFormatFactory
               implements FromCollection , WithStrictNullComparison, WithEvents, WithHeadings, WithColumnWidths, WithStyles , JsonSerializable
{
 
    use CSVImportableFileFormatFactorySerilizing , CSVImportableFileFormatFactorySerilizing , PublicsGetters , PublicSetters;

    protected ?Collection $formatDataCollection = null;
    protected string $fileName;
    protected  string $writerType = "Csv";
    protected array $headers = [];
    protected array $validColumnFormatInfoCompoenents = [];
    protected int $firstRowHeight = 30;
    protected int $firstRowWidth = 180;

    protected array $modelColumnComponents = [];
    protected array $relationshipsColumnComponents = [];
    
    abstract protected function getColumnFormatInfoCompoenents() : array;

    public function __construct(string $fileName, array $headers = [])
    { 
        $this->setFileName($fileName)->setHeaders($headers)
                                    ->setChildColumnFormatInfoCompoenents()
                                    ->setModelColumnComponents()->setRelationshipColumnComponents()
                                    ->setFormatDefaultCollection();
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
    
    public function getRawContent()
    {
        return $this->initPixelExcelFormatFactoryLib()->raw($this ,  $this->writerType );
    }

    public function collection()
    {
        return $this->formatDataCollection ;
    }

    protected function getHeadingsKeysArray() : array
    {
        $headings = [];

        foreach($this->headings() as $heading)
        {
            $headings[$heading] = null;
        }
        return $headings;
    }

    protected function setFormatDefaultCollection() : void
    {
        $this->setDataFileToManuallyChange( [] );
    }

    
    protected function setChildColumnFormatInfoCompoenents() : self
    {
       $this->useValidColumnFormatInfoCompoenents( $this->getColumnFormatInfoCompoenents()  );
        return $this;
    }

    protected function useValidColumnFormatInfoCompoenents(array $components) : self
    {
        $this->validColumnFormatInfoCompoenents =  array_filter(
                                                                $components ,
                                                                function($component)
                                                                {
                                                                    return $component instanceof CSVFormatColumnInfoComponent;
                                                                });
        return $this;
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
 
    public function sortValueArray(array &$array)
    { 
        sort($array); 
    }
 
    protected function setRelationshipColumnComponents() : self
    {
        $components = [];
        foreach($this->validColumnFormatInfoCompoenents as $componenet )
        {
            if($componenet->isItRelationColumn())
            {
                $relationName = $componenet->getRelationName();
                $columnFielldName = $componenet->getDatabaseFieldName();

                if( isset($componenets[$relationName]) )
                {
                    $components[$relationName][$columnFielldName] = $componenet;
                    continue;
                }

                $components[$relationName]= [ $columnFielldName => $componenet];
            }
        }
        $this->relationshipsColumnComponents = $components;
        return $this;
    }

 

    protected function setModelColumnComponents() : self
    {
        foreach($this->validColumnFormatInfoCompoenents  as $componenet)
        {
            if(!$componenet->isItRelationColumn())
            {
                $this->modelColumnComponents[$componenet->getDatabaseFieldName()] = $componenet;
            }
        }
        return $this;
    }
   
    public function headings(): array
    { 
        $headings = array_map(function($component)
                    {
                            return $component->getColumnHeaderName();
                    } , $this->validColumnFormatInfoCompoenents);

        $this->sortValueArray($headings);
        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {

                foreach($this->validColumnFormatInfoCompoenents as $component)
                {
                    $this->setColumnValidation($component, $event); 
                    $event->sheet->getColumnDimension($component->getColumnCharSymbol())->setAutoSize(true);
                } 

                $this->setFirstRowHeight($event);
                $this->setFirstRowWidth($event);
            },
        ];
    }


    //this is the old func
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