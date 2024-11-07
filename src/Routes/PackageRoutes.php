<?php

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    "exported-file-downloading/{fileName}",
     function (Request $request, string $fileName) 
    {
       $request->hasValidSignature();
       $exportedDataFilesInfoManager = new ExportedDataFilesInfoManager();
       $fileRealPath = $exportedDataFilesInfoManager->getFileRealPath($fileName);
       
       if ($fileRealPath) 
       {
           return response()->download($fileRealPath, $fileName);
       }

       return new Exception("The Requested Data File Is Not Found Or Expired !");

    })->name("exported-file-downloading");