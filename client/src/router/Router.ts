import api_routes from "@/api_routes";

const url = process.env.VUE_APP_API_BASE_URL

interface StringMap { [key: string]: string; }

export type Route = {
    method: string,
    path: string,
    abs : string
}

type Query = StringMap

export default class Router {

    static getRoute(name : string, ...params : any[] ) : Route | null {
        let route = Router.findRoute(name);

        if(!route) {
            return null
        }

        let path = Router.parseRouteStrings(route.path,[...params[0]])

        return {
            path: path,
            abs: url + path,
            method: route.method,
        }
    }

    static findRoute(name : string) : { path: string; method: string; name: string } | null {
        return api_routes.find( route => route.name === name) || null
    }

    static parseRouteStrings(routeString : string ,params : any[]) {
        params.forEach((param : any) => {
            routeString = routeString.replace(/{.*?}/,param)
        })
        return routeString
    }

    static buildQuery(url : string, { page = null, perPage = null, query = null } : { page : number | null, perPage : number | null, query : Query | null } ) {

        if(url || perPage || query) {
            url += '?'
        }

        if(page) {
            url += '&page=' + page
        }

        if(perPage) {
            url += '&per_page=' + perPage
        }

        if(query) {
            for(const key in query) {
                if(query[key] !== null) {
                    url += `&${key}=${query[key]}`
                }
            }
        }
        return url
    }
}
