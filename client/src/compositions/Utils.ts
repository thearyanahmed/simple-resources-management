import moment from "moment"
import {PaginatedResponse, ResourceForm} from "@/compositions/Resource"
import {PaginationLinks, PaginationMeta} from "@/compositions/Pagination";
import {ErrorBag} from "@/plugins/Request";

export function displayDate(date : Date) : string {
    // format 8:04pm 4th sept, 21
    return moment(date).format('h:mm a DD MMM, YY')
}

export function objectToFormData(form : ResourceForm | Object) : FormData {
    const formData = new FormData

    for (const key in form) {
        // @ts-ignore
        formData.append(key,form[key])
    }

    return formData
}

export function saveFileFromStream(blobParts : BlobPart[], saveAs: string) {
    const blob = new Blob(blobParts, { type: 'application/pdf' })

    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')

    link.download = saveAs
    link.classList.add('hidden')
    link.href = url

    document.body.appendChild(link)
    link.click()

    link.remove()
}

export function emptyPaginatedResponse() : PaginatedResponse {
    return {
        data: [],
        links: {} as PaginationLinks,
        meta: {} as PaginationMeta,
    }
}

export function emptyErrorBag() : ErrorBag {
    return {
        message: null,
        errors: [],
    }
}