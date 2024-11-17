<?php

namespace ExpImpManagement\Interfaces;

interface PixelExcelFormatFactoryLib
{
    
    /**
     * Must export a file and return its raw contentt without storing or streaming it 
     */
    public function raw($export, string $writerType) ;
 
    /**
     * {@inheritdoc}
     */
    public function download($export, string $fileName, string $writerType = null, array $headers = []);
}