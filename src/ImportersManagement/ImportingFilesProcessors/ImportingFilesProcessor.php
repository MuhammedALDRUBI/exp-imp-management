<?php

namespace ExpImpManagement\ImportersManagement\ImportingFilesProcessors;
 
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor; 

class ImportingFilesProcessor extends TemporaryFilesProcessor
{
 
    protected string $TempFilesFolderName = "tempFiles/ImportingTempFiles";

}
