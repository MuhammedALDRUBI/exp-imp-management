<?php

namespace ExpImpManagement\ImportersManagement\Interfaces;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;

interface SelfConstructableCSVImporter
{
    public function getModelClassForSelfConstructing() : string;
    public function getDataValidationRequestFormClassForSelfConstructing() : string;
    public function getImportableTemplateFactoryForSelfConstructing() : CSVImportableFileFormatFactory;
}