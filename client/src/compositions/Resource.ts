export type ResourceType = {
    Link : 'link'
    HtmlSnippet : 'html_snippet'
    File : 'file'
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

export type PaginationLinks = {
    first ?: string
    last ?: string
    prev ?: string
    next ?: string
}

export type PaginatedResponse = {
    data : Resource[]
    links: PaginationLinks
}