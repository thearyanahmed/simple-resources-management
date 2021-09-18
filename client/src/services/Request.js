import axios from "axios"

import Router from "./Router"

export default class Request {
    constructor() {
        this.actingAsAdmin = false
    }

    success(callback) {
        this.onSuccess = callback
        return this
    }

    error(callback) {
        this.onError = callback
        return this
    }

    endsWith(callback) {
        this.onCompletion = callback
        return this
    }

    to(name,params) {
        this.endpoint = Router.getRoute(name,params || [])
        return this
    }

    with(requestData) {
        this.requestData = requestData
        return this
    }

    headers(data = {}) {
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

        let headers = {
            'Accept': 'application/json',
            ...this.requestHeaders
        }

        if(! this.actingAsAdmin) {
            delete headers['user_email']
        } else {
            headers['user_email'] = 'admin@admin.com'
        }

        const data = this.requestData

        const config = {
            headers: headers,
            url: this.endpoint.abs,
            params: data,
            method: this.endpoint.method,
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
                this.errorBag = {
                    message: err.response.data.message,
                    errors: []
                }

                //for(const prop in (err.response.data.errors || [])) {
                    // if(err.response.data.errors.hasOwnProperty(prop)) {
                    //     this.errorBag.errors.push(...err.response.data.errors[prop])
                    // }
                //}

                this.onError(err.response.data,this.errorBag)
            })
            .finally(() => {
                if(!this.onCompletion) {
                    return
                }
                this.onCompletion()

            })

        return this.request
    }
}
