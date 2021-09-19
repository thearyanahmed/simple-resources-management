<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateResourceRequest extends CreateResourceRequest
{
    // check CreateResourceRequest
    protected $fileRules = [
        'file' => 'nullable|file|mimes:pdf|max:5120'
    ];
}
