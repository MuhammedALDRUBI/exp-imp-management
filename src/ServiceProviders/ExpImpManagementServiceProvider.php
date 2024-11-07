<?php

namespace ExpImpManagement\ServiceProviders;
 
use ExpImpManagement\Interfaces\PixelExcelExpImpLib;
use ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib;
use ExpImpManagement\PixelAdapters\PixelExcelExpImpLibAdapter;
use ExpImpManagement\PixelAdapters\PixelExcelFormatFactoryLibAdapter;
use Maatwebsite\Excel\Files\Filesystem;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\QueuedWriter;
use Maatwebsite\Excel\Writer;
use Maatwebsite\Excel\Reader;
use PixelDomPdf\Interfaces\PixelPdfNeedsProvider;

class ExpImpManagementServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadPackageRoutes();
        //scedule OldDataExportersDeleterJob job here also
        
        // $this->publishes(
            // [__DIR__ . "/../../config/validator-lib-config.php" => config_path("validator-lib-config.php") ] ,
            // 'validator-lib-config'
        // );

    }
    protected function loadPackageRoutes() : void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/PackageRoutes.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPdfLib();
        $this->registerPixelExcelExpImpLib();
        $this->registerPixelExcelFormatFactoryLib();
    }

    protected function registerPdfLib() : void
    {
        $this->app->bind(PixelPdfNeedsProvider::class , function($app)
        {
            return $app["dompdf"];
        });
    }

    protected function registerPixelExcelExpImpLib() : void
    {
        $this->app->bind(PixelExcelExpImpLib::class , function($app)
        {
            return new PixelExcelExpImpLibAdapter();
        });
    }

    protected function registerPixelExcelFormatFactoryLib() : void
    {
        $this->app->bind(PixelExcelFormatFactoryLib::class , function($app)
        {
            return new PixelExcelFormatFactoryLibAdapter(
                                                            $app->make(Writer::class),
                                                            $app->make(QueuedWriter::class),
                                                            $app->make(Reader::class),
                                                            $app->make(Filesystem::class)
                                                        );
        });
    }
    
}
