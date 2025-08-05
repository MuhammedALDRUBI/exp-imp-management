<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\Jobs\OldDataExportersDeleterJob;

trait OldDataExportersDeleterJobMethods
{

    protected static ?string $OldDataExportersDeleterAltJob = null;
    
    protected static function getOldDataExportersDeleterDefaultJobClass() : string
    {
        return OldDataExportersDeleterJob::class;
    }

    /**
     * $jobClass class must be a child type of ExpImpManagement\ExportersManagement\Jobs\OldDataExportersDeleterJob class
     */
    public static function setOldDataExportersDeleterAlternativeJob(string $jobClass) : void
    {
        if(is_subclass_of( $jobClass , static::getOldDataExportersDeleterDefaultJobClass() ))
        {
            static::$OldDataExportersDeleterAltJob = $jobClass;
        }
    }

    public static function getOldDataExportersDeleterJobClass() : string
    {
        return static::$OldDataExportersDeleterAltJob ?? static::getOldDataExportersDeleterDefaultJobClass();
    }

}