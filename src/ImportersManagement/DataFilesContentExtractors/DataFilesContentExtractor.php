<?php

namespace ExpImpManagement\ImportersManagement\DataFilesContentExtractors;

use Illuminate\Support\Collection;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;

abstract class DataFilesContentExtractor
{
    protected string $filePathToProcess ;
    protected ?TemporaryFilesProcessor $filesProcessor = null;

    /**
     * @param TemporaryFilesProcessor|null $filesProcessor
     * @return $this
     */
    public function setFilesProcessor(?TemporaryFilesProcessor $filesProcessor): self
    {
        $this->filesProcessor = $filesProcessor;
        return $this;
    }

    /**
     * @param string $filePathToProcess
     * @return $this
     */
    public function setFilePathToProcess(string $filePathToProcess): self
    {
        $this->filePathToProcess = $filePathToProcess;
        return $this;
    }

    abstract public function getData() : Collection;
}
