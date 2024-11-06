<?php

namespace ExpImpManagement\ExportersManagement\Responders;

abstract class StreamingResponder extends Responder
{
 
    protected string $FileFullName ;

    /**
     * @param string $FileFullName
     * @return $this
     */
    public function setFileFullName(string $FileFullName): self
    {
        $this->FileFullName = $FileFullName;
        return $this;
    }
 
}
