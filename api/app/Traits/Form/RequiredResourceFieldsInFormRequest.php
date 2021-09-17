<?php

namespace App\Traits\Form;

use App\Models\Resource;
use Exception;

trait RequiredResourceFieldsInFormRequest
{
    /**
     * @return string[]
     * @throws Exception
     */
    protected function requiredRelatedResources(string $relatedResourceType) : array
    {
        if($relatedResourceType === Resource::RESOURCE_LINK) {
            return ['link','opens_in_new_tab'];
        }

        if($relatedResourceType === Resource::RESOURCE_HTML_SNIPPET) {
            return ['markup','description'];
        }

        if($relatedResourceType === Resource::RESOURCE_FILE) {
            return ['file'];
        }

        throw new Exception('unsupported resource type');
    }
}
