<?php

namespace ExpImpManagement\Interfaces;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Collection;

interface PixelExcelExpImpLib
{

     /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|string
     */
    public function downloadDataFile($path, ?callable $callback = null) : StreamedResponse|string;

      /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return string
     */
    public function exportDataFile($path, ?callable $callback = null) : string;
    
    /**
     * @param string        $path
     * @param callable|null $callback 
     *
     * @return Collection
     */
    public function importDataFile($path, ?callable $callback = null) : Collection;

     /**
     * Manually set data apart from the constructor.
     *
     * @param Collection|Generator|array $data 
     */
    public function setExportingData($data) : PixelExcelExpImpLib;
}