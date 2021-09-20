<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateResourceRequest extends CreateResourceRequest
{
    // check CreateResourceRequest
    protected function fileRules()
    {
        if($this->hasFile('file')) {
            return [
                'file' => 'required|file|mimes:pdf|max:5120'
            ];
        }

        return [];
    }
}
