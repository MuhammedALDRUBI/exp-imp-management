<?php

namespace ExpImpManagement\PixelAdapters;

use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PixelExcelExpImpLibAdapter extends FastExcel implements PixelExcelExpImpLib
{
    
     /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     */
    public function downloadDataFile($path, ?callable $callback = null) : StreamedResponse|string
    {
        return $this->download($path , $callback);
    }

      /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return string
     */
    public function exportDataFile($path, ?callable $callback = null) : string
    {
        return $this->export($path , $callback);
    }
    
    /**
     * @param string        $path
     * @param callable|null $callback 
     *
     * @return Collection
     */
    public function importDataFile($path, ?callable $callback = null) : Collection
    {
        return $this->import($path , $callback);
    }

     /**
     * Manually set data apart from the constructor.
     *
     * @param Collection|Generator|array $data 
     */
    public function setExportingData($data) : PixelExcelExpImpLib
    {
        $this->data($data);
        
        return $this;
    }

}