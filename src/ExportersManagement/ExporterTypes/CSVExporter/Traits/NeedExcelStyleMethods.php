<?php

namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Traits;


use OpenSpout\Common\Entity\Style\Style;
use Exception;

trait NeedExcelStyleMethods
{
        /**
     * @param $style
     * @return $this
     * @throws Exception
     */
    public function setHeaderStyle($style): self
    {
        if (!$style instanceof Style) {
            throw new Exception("The Given Style Is Not valid !");
        }
        $this->excel->headerStyle($style);
        return $this;
    }
 
    /**
     * @param $style
     * @return $this
     * @throws Exception
     */
    public function setRowStyle($style): self
    {
        if (!$style instanceof Style) {
            throw new Exception("The Given Style Is Not valid !");
        }
        $this->excel->rowsStyle($style);
        return $this;
    }
}