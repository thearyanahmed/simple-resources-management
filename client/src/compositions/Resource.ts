import { PaginationLinks, PaginationMeta } from "@/compositions/Pagination";
import {StringMap} from "@/compositions/QueryParams";

export enum ResourceType {
    link = 'link',
    html_snippet = 'html_snippet',
    file = 'file',
}

export type Link = {
    link : string
    opens_in_new_tab: boolean
}

export type HtmlSnippet = {
    markup : string
    description: boolean
}

export type File = {
    abs_path : string
    path : string
}

export type Resource = {
    id : number
    title: string
    created_at: Date
    updated_at: Date
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
    'link' : 'link',
    'html_snippet' : 'html snippet',
    'file' : 'pdf',
}

export type ResourceColors = StringMap

export const ResourceTypeColors : ResourceStrings = {
    'link' : 'bg-red-300',
    'html_snippet' : 'bg-green-400',
    'file' : 'bg-purple-300',
}

