<?php

namespace ExpImpManagement\Interfaces;

interface PixelExcelFormatFactoryLib
{
 
    /**
     * {@inheritdoc}
     */
    public function download($export, string $fileName, string $writerType = null, array $headers = []);
}