<?php

namespace ExpImpManagement\ImportersManagement\RequestForms;

use ValidatorLib\CustomFormRequest\BaseFormRequest;

class UploadedFileRequestForm extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     * @param $data
     * @return array
     */
    public function rules($data): array
    { 
        return [
            'file' => [    
                            "required", 
                            "mimetypes:text/csv,application/csv,text/plain",
                      ],
        ];
    }
    

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "file.required" => "A file is required", 
            "file.mimetypes" => "The Allowed uploaded File Must Be Valid CSV File",
        ];
    }
}
