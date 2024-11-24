<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use Illuminate\Support\Str;

trait FileNameProccessing
{

    public function useTheSameDocumentTitle() : self
    {
        $this->outoutUniqueDocumentTitle = false;
        return $this;
    }

    public function useUniqueDocumentTitle() :  self
    {
        $this->outoutUniqueDocumentTitle = true;
        return $this;
    }

    public function composeFileFullName() : string
    {
        return $this->fileName . "." . $this->getDataFileExtension();
    }

    protected function setFileFullName() : self
    { 
        $this->fileFullName =  $this->composeFileFullName(); 
        return $this;
    }
    
    /**
     * return Only File Name (Document Title + Date  , Doesn't Contain The Extension )
     *To Get Full name With Extension use $this->fileName
     * @return string
     */
    public function composeFileName(string $documentTitle) : string
    {
        $name = $this->sanitizeFileCustomName($documentTitle);
        return $this->outoutUniqueDocumentTitle 
               ? 
               Str::slug( $name , "_") .  date("_Y_m_d_his") 
               :
               $name;
    }

    protected function sanitizeFileCustomName(string $name) : string
    {
        return explode("." , $name)[0];
    }

    /**
     * @param string $name
     * @param string $extension
     * @return $this
     * @throws Exception 
     */
    protected function setFileName(string $documentTitle ) : self
    {
        $this->fileName =  $this->composeFileName($documentTitle) ;
        return $this;
    }
    /**
     * @return $this
     */
    protected function setFileNames(string $documentTitle) : void
    {
        $this->setFileName($documentTitle)->setFileFullName();
    }


}