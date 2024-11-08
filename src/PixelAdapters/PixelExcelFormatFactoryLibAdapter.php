<?php

namespace ExpImpManagement\PixelAdapters;

use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use Maatwebsite\Excel\Excel;

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
}