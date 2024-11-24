<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;

interface SelfConstructableExporter
{
    public function getModelClassForSelfConstructing() : string;
}