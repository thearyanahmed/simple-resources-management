export interface StringMap { [key: string]: string; }

export enum OrderDirection {
    Asc = 'asc',
    Desc = 'desc'
}

export type Page  = number

export type QueryParams = {
    per_page ?: number | null
    order_by ?: string
    order_dir ?: OrderDirection
    page ?: Page | null
    title ?: string
    resource_type ?: string
}