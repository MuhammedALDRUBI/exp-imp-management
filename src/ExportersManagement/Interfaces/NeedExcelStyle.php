<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;

interface NeedExcelStyle
{
    public function setHeaderStyle( $style);
    public function setRowStyle( $style);
}
