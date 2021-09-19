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
        method: 'POST',
        path: '/resources/{id}'
    },
    {
        name: 'resources.delete',
        method: 'DELETE',
        path: '/resources/{id}'
    },
]
