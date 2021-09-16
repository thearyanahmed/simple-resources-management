<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        // first we make sure resource_type is given
        // not passing request->all() as we only need to check 'resource_type'

        $resourceTypeRule = ['resource_type' => 'required|string|in:' . Resource::RESOURCE_HTML_SNIPPET . ',' . Resource::RESOURCE_LINK . ',' . Resource::RESOURCE_FILE ];

        Validator::validate(
            ['resource_type' => $this->request->get('resource_type')], // data
            $resourceTypeRule
        );

        // if given, we dynamically pull the resource type rules
        // the array key will exist if the code reaches this point, because we are checking "in:link,html_snippet,pdf"

        return array_merge(
            $this->getResourceCreationRules(
                $this->request->get('resource_type')
            ),
            ['title' => 'required|string|max:150'],
            $resourceTypeRule,
        );
    }

    private function getResourceCreationRules(string $rule): array
    {
        return ([
            Resource::RESOURCE_LINK => [
                'link'             => 'required|url|max:250',
                'opens_in_new_tab' => 'required|boolean',
            ],
            Resource::RESOURCE_HTML_SNIPPET => [
                'description' => 'required|string|255',
                'markup'      => 'required|string|1000',
            ],
            Resource::RESOURCE_FILE => [
                'file' => 'required|file|mimes:pdf|max:5120'
            ],
        ])[$rule];
    }
}
