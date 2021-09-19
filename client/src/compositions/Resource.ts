import { PaginationLinks, PaginationMeta } from "@/compositions/Pagination";
import {StringMap} from "@/compositions/QueryParams";

export enum ResourceType {
    link = 'link',
    html_snippet = 'html_snippet',
    file = 'file',
}

const link = ResourceType.link
const html_snippet = ResourceType.html_snippet
const file = ResourceType.file

export type Link = {
    link : string
    opens_in_new_tab: boolean
}

export type HtmlSnippet = {
    markup : string
    description: string
}

export type File = {
    abs_url : string
    path : string
}

export type Resource = {
    id : number
    title: string
    created_at ?: Date
    updated_at ?: Date
    type: ResourceType
    link ?: Link
    html_snippet ?: HtmlSnippet
    file ?: File
}

export type PaginatedResponse = {
    data : Resource[]
    links: PaginationLinks
    meta : PaginationMeta
}

export type ResourceStrings = StringMap

export const ResourceTypeText : ResourceStrings = {
    link : 'link',
    html_snippet : 'html snippet',
    file : 'file (pdf)',
}

export type ResourceColors = StringMap

export const ResourceTypeColors : ResourceStrings = {
    'link' : 'bg-red-300',
    'html_snippet' : 'bg-green-400',
    'file' : 'bg-purple-300',
}

interface CreatesFormData {
    toFormData() : FormData
}

export type ResourceForm  = {
    title: string,
    resource_type: string,
    link ?: string
    opens_in_new_tab ?: boolean
    file ?: string
    markup ?: string
    description ?: string
    _method?: string
}

export function resourceToFormFactory(resource : Resource, type: ResourceType) : ResourceForm {

    const form : ResourceForm = {
        title: resource.title,
        resource_type: type,
    }

    if(type === ResourceType.link) {
        form.opens_in_new_tab = resource.link?.opens_in_new_tab
        form.link = resource.link?.link // ugly
    } else if (type === ResourceType.file) {
        form.file = resource.file?.abs_url
    } else if (type === ResourceType.html_snippet) {
        form.markup = resource.html_snippet?.markup
        form.description = resource.html_snippet?.description
    }
    return form
}
