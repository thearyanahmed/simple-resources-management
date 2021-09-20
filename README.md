# Simple Resources Management

## Installation


- Current requirement: `php 8` , `laravel 8`, `composer 2` , `node 14.17.x` or above, `vue3` , `sqlite`

```bash
git clone git@github.com:thearyanahmed/simple-resources-management.git
```


```bash
cd simple-resources-management
```

We can manually setup the project or use docker. 

## Setup with docker

With docker, the application will use port 8000 for api end and port 5000 for the frontend.
To run the app with docker, run the following commands,

- `chmod +x startx`
- `./startx`

Thats it.

**Note** 
```txt
This will take some time the first time, as it pulls a docker image ( for dev mode, not optimized ), pulls in the composer and node dependencies,
sets up db, runs migration, seeds the db and does a npm build on vue 3.
So, when we see vue app has finished its build, we can then use the app on https://localhost:5000.
```

## Manual installation

API 

Run the following commands,

- `cd api`
- `cp .env.example .env`
- `touch database/database.sqlite`
- `php artisan migrate --seed`

Client
Run the following commands,

- `cd client`
- `cp .env.example .env`
- `npm i`
- `npm run build` (or `npm run serve`, running serve will start the app port 8080 by default )

if we have ran npm build, then
- `npm install -g serve`
- `serve -s dist`


**Note**
```txt
While seeding manually, it will not create multiple PDF files in the storage, it will use the same file's path so it doesn't take too much space.

And seeding during running test should not create any new pdf file, as Storage is faked during boot.

And for creating a File (PDF) type resource, it will be uploaded and while updating, it should delete the previous one if needed.
```

## Tests 

There are a bunch of tests in the api/tests directory. You can run
```bash
php artisan test
```

in the api directory to test them.

[https://github.com/thearyanahmed/simple-resources-management/actions](Github workflows was also setup to run tests on push).

## Authentication

As this app is without authentication, but there were 2 types of role, *visitor* and *administrator*. So, I've created a pseudo authentication to make sure we do not allow
the *visitor* to add, edit or delete any resource.

For this purpose, a simple header `{ user_email: admin@admin.com }` is being used (hardcoded).

From the frontend, this was replicated using `asAdmin()` and `asRegularUser()` functions in the frontend http client ( `client/src/plugins/Request.ts` ).

## Frontend Routes

Visitor routes
- `/` All of the resources for the visitor 

Admin routes
- `/admin/resources` list view and option to go to create, edit
- `/admin/resources/create` create view
- `/admin/resources/:id/edit` edit view


## Api routes

Visitor routes
- `/resources` all resources
- `resources/{id}/download` download pdf files

Admin routes
- `/resources` (post method) creates a resource 
- `/resources/{id}/edit` (get method) retrieves resource for editing
- `/resources/{id}` (put method) updates a resource 
- `/resources/{id}` (delete method) deletes a resource 

## DB Design

Currently, we have 4 tables, their associted models are,

- Resource
- Link
- File
- HtmlSnippet

They are stored in a one to one polymorphic relationship. The relationship is between a `Resource` and any one of the other 3 resources. `Resource` table holds 
id and title and other tables hold data respective to their kind.

For example,

links table holds link and opens_in_new_tab column,
html_snippets table holds description and markup column,
files table holds file path, absoulte path and file disk.


the end.