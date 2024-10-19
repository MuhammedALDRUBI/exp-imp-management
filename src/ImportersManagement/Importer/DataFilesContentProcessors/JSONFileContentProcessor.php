<?php

namespace ExpImpManagement\ImportersManagement\Importer\DataFilesContentProcessors;

use Exception;

class JSONFileContentProcessor extends DataFileContentProcessor
{

    /**
     * @return array
     * @throws Exception
     */
    public function getData(): array
    {
        $jsonContent = $this->filesProcessor->getFileContent(  $this->filePathToProcess );
        return json_decode($jsonContent , true );
    }
}
