import Request from "@/services/Request";

export function getAllResources({ per_page = 10,  }){
    return (new Request)
        .to('resources.index')
        .asRegularUser()
        .with({
            per_page,
        })
}