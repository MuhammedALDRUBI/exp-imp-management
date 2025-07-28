<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;

interface NeedExcelStyle
{
    public function setHeaderStyle( $style)  :NeedExcelStyle;
    public function setRowStyle( $style) : NeedExcelStyle;
}
