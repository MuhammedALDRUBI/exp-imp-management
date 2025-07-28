<?php

namespace ExpImpManagement\Interfaces;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface PixelExcelFormatFactoryLib
{
    
    public function downloadFile($export, string $fileName, ?string $writerType = null, array $headers = []) : BinaryFileResponse;

    
    /**
     * Must export a file and return its raw contentt without storing or streaming it 
     */
    public function exportFileRawContent($export, string $writerType) : string;

}