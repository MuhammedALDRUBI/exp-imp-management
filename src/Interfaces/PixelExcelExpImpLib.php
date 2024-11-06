<?php

namespace ExpImpManagement\Interfaces;

interface PixelExcelExpImpLib
{

     /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return string
     */
    public function download($path, callable $callback = null);

      /**
     * @param string        $path
     * @param callable|null $callback
     *
     * @return string
     */
    public function export($path, callable $callback = null);
    
    /**
     * @param string        $path
     * @param callable|null $callback 
     *
     * @return Collection
     */
    public function import($path, callable $callback = null);

     /**
     * Manually set data apart from the constructor.
     *
     * @param Collection|Generator|array $data 
     */
    public function data($data);
}