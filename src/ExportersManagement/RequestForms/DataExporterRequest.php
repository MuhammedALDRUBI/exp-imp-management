<?php

namespace ExpImpManagement\ExportersManagement\RequestForms;

use ValidatorLib\CustomFormRequest\BaseFormRequest;

class DataExporterRequest extends BaseFormRequest
{

    /**
     * @param $data
     * @return array[]
     */
    public function rules($data): array
    {
        return [
            'type' => ['bail' , 'required' , 'string' ],
        ];
    }
}
