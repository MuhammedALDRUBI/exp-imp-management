<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories;

use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

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
    
    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count =  201;
                $column_count = 8;

                // set dropdown columns
                $number_column = 'A';
                $type_column = 'B';
                $size_column = 'C';
                $floor_column = 'D';
                $status_column = 'E';
                $area_column = 'F';
                $notes_column = 'H';




                $area_options = FireSysArea::whereNotNull('parent_id')->pluck('name')->toArray();
                $type_options = [
                    'Galvanized Single',
                    'Galvanized Double',
                    'Stainlesssteel Single',
                    'Stainlesssteel Double'
                ];
                $size_options = ['1.00', '1.50', '2.50', '1.00 & 2.50'];
                $floor_options = ['1st Floor', '2nd Floor', '3rd Floor'];
                for ($i = 4; $i <= 30; $i++) {
                    array_push($floor_options, "{$i}th Floor");
                }
                $status_options = ['Normal', 'Work & has faults', 'Work & Need Spare Parts', 'Not working & Need Spare Parts', 'Normal But Manual', 'Damaged'];

                $main_area_column = 'G';

                $main_area_options = FireSysArea::whereNull('parent_id')->pluck('name')->toArray();

                // set dropdown options
                $this->setDropDownColumnValidation($main_area_column, $main_area_options, $row_count, $column_count, $event);
                $this->setDropDownColumnValidation($area_column, $area_options, $row_count, $column_count, $event);
                $this->setDropDownColumnValidation($type_column, $type_options, $row_count, $column_count, $event);
                $this->setDropDownColumnValidation($size_column, $size_options, $row_count, $column_count, $event);
                $this->setDropDownColumnValidation($status_column, $status_options, $row_count, $column_count, $event);
                $this->setDropDownColumnValidation($floor_column, $floor_options, $row_count, $column_count, $event);



                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(180);
            },
        ];
    }
    public function setDropDownColumnValidation($column, $options, $row_count, $column_count, AfterSheet $event)
    {
        // set dropdown list for first data row
        $validation = $event->sheet->getCell("{$column}2")->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

        // clone validation to remaining rows
        for ($i = 3; $i <= $row_count; $i++) {
            $event->sheet->getCell("{$column}{$i}")->setDataValidation(clone $validation);
        }

        // set columns to autosize
        for ($i = 1; $i <= $column_count; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $event->sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}