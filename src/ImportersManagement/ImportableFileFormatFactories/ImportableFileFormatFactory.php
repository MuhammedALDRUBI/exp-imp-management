<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories;

abstract class ImportableFileFormatFactory
{
    abstract public function downloadFormat();
}