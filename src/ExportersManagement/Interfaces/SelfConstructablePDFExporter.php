<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;

interface SelfConstructablePDFExporter
{
    public function getViewRelevantPathForSelfConstructing() : string;
}