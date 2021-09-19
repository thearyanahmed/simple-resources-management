import axios, {AxiosRequestConfig, Method, AxiosResponse} from "axios"

import Router, {Route} from "../router/Router"

type CallbackHandler = (data: any | null) => any
type RequestData = object | FormData
type Headers = object
type ErrorBag = {
    message: string | null,
    errors: string[]
}

export default class Request {
    private actingAsAdmin: boolean;
    private onSuccess: CallbackHandler | null;
    private onError: CallbackHandler | null;
    private onCompletion: CallbackHandler | null;
    private endpoint: Route | null;
    private requestData: RequestData | null;
    private requestHeaders: Headers | null;
    private request: Promise<any> | null;
    private response: AxiosResponse<any> | null;
    private data: any;
    private errorBag: ErrorBag | null;

    constructor() {
        this.actingAsAdmin = false
        this.onSuccess = null
        this.onError = null
        this.onCompletion = null
        this.endpoint = {} as Route
        this.requestData = null
        this.requestHeaders = null
        this.request = null
        this.errorBag = null
        this.response = null
    }

    success(callback: CallbackHandler) {
        this.onSuccess = callback
        return this
    }

    error(callback: CallbackHandler) {
        this.onError = callback
        return this
    }

    endsWith(callback : CallbackHandler) {
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

    headers({ data = {}} : { data: Headers | null } ) {
        this.requestHeaders = data
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

        const data = this.requestData

        const config : AxiosRequestConfig = {
            headers: headers,
            url: this.endpoint.abs,
            params: data,
            method: (this.endpoint.method as Method),
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

                this.errorBag = {} as ErrorBag

                for(const prop in (err.response.data.errors || [])) {
                    if(err.response.data.errors[prop] !== null) {
                        this.errorBag.errors.push(...err.response.data.errors[prop])
                    }
                }

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
