import api_routes from "@/api_routes";

const url = process.env.VUE_APP_API_BASE_URL

export default class Router {

    static getRoute(name, ...params ) {
        let route = Object.assign({}, Router.findRoute(name));

        if(!route) {
            return null
        }
        let path = route.path

        path = Router.parseRouteStrings(path,[].concat.apply([], params[0]))

        route.path = path
        route.abs = url + path

        return route
    }

    static findRoute(name) {
        return api_routes.find( route => route.name === name) || null
    }

    static parseRouteStrings(routeString,params) {
        params.forEach(param => {
            routeString = routeString.replace(/{.*?}/,param)
        })
        return routeString
    }

    static buildQuery(url, { page, perPage, query= {} } ) {

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
