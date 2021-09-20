import axios, {AxiosRequestConfig, Method, AxiosResponse} from "axios"

import Router, {Route} from "@/router/Router"
import {QueryParams} from "@/compositions/QueryParams"

type CallbackHandler = (data: any | null) => any
type RequestData = QueryParams | FormData | object

export type ErrorBag = {
    message?: string | null,
    errors: string[]
    statusCode ?: number | null
}

enum ResponseType {
    json= 'json',
    blob = 'blob',
}

export default class Request {
    private actingAsAdmin: boolean;
    private onSuccess: CallbackHandler | null;
    private onError: CallbackHandler | null;
    private onCompletion: CallbackHandler | null;
    private endpoint: Route | null;
    private requestData: RequestData | null;
    private params : QueryParams | null;
    private requestHeaders: Object | null;
    private request: Promise<any> | null;
    private response: AxiosResponse | null;
    private responseType: ResponseType;
    private data: any;
    private errorBag: ErrorBag | null;

    constructor() {
        this.actingAsAdmin = false
        this.onSuccess = null
        this.onError = null
        this.onCompletion = null
        this.endpoint = {} as Route
        this.requestData = null
        this.params = null
        this.requestHeaders = null
        this.request = null
        this.errorBag = null
        this.response = null
        this.responseType = ResponseType.json
    }

    success(callback: CallbackHandler) {
        this.onSuccess = callback
        return this
    }

    error(callback: CallbackHandler) {
        this.onError = callback
        return this
    }

    finally(callback : CallbackHandler) {
        this.onCompletion = callback
        return this
    }

    to(name: string,params: any[]) {
        this.endpoint = Router.getRoute(name,params || [])
        return this
    }

    with(requestData : RequestData) {
        this.requestData = requestData
        return this
    }

    queryParams(params : QueryParams) {
        this.params = params
        return this
    }

    headers(header : object ) {
        this.requestHeaders = header
        return this
    }

    asAdmin() {
        this.actingAsAdmin = true
        return this
    }

    asRegularUser(){
        this.actingAsAdmin = false
        return this
    }

    download() {
        this.responseType = ResponseType.blob
        this.send()
    }

    /**
     * Send the actual request.
     *
     * @returns {Promise<void>}
     */
    send() {

        if(this.endpoint === null) {
            throw new ReferenceError('reference to invalid route.')
        }

        const headers = {
            'Accept': 'application/json',
            ...this.requestHeaders
        }

        if(! this.actingAsAdmin) {
            // @ts-ignore
            delete headers['user_email']
        } else {
            // @ts-ignore
            headers['user_email'] = 'admin@admin.com'
        }


        const config : AxiosRequestConfig = {
            headers: headers,
            url: this.endpoint.abs,
            params: this.params,
            data: this.requestData,
            method: (this.endpoint.method as Method),
            responseType: this.responseType,
        }

        this.request = axios(config)
            .then(res => {
                if(! this.onSuccess) {
                    return
                }
                this.response = res
                this.data = res.data
                this.onSuccess(res.data)
            })
            .catch(err => {
                if(! this.onError) {
                    return
                }
                const errorBag : ErrorBag = {
                    message: err.response?.data?.message ?? 'sorry something went wrong.',
                    errors: [],
                    statusCode: err?.response?.status,
                }

                for(const prop in (err.response.data.errors || [])) {
                    if(err.response.data.errors[prop] !== null) {
                        errorBag.errors.push(...err.response.data.errors[prop])
                    }
                }

                this.errorBag = errorBag
                this.onError(this.errorBag)
            })
            .finally(() => {
                if(!this.onCompletion) {
                    return
                }
                this.onCompletion(null)

            })

        return this.request
    }
}
