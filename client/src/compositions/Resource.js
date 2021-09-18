import Request from "@/services/Request";

export function getAllResources({ per_page = 10,  }){
    (new Request)
        .to('resources.index')
        .asRegularUser()
        .with({
            per_page,
        })
        .success((res) => {
            console.log('success',res)
        })
        .error((err) => {
            console.log('err',err)
        })
        .send()
}