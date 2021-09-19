export default  [
    {
        name: 'resources.index',
        method: 'GET',
        path: '/resources'
    },
    {
        name: 'resources.edit',
        method: 'GET',
        path: '/resources/{id}/edit'
    },
    {
        name: 'resources.update',
        method: 'PATCH',
        path: '/resources/{id}'
    },
]
