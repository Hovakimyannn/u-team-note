# ![U-Team note](logo.png)


# Getting started

## Installation

Clone the repository

    git clone git@github.com:Hovakimyannn/u-team-note.git note

Switch to the repo folder

    cd note

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**) [Environment variables](#environment-variables)


    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone git@github.com:Hovakimyannn/u-team-note.git note
    cd note
    composer install
    cp .env.example .env
    php artisan key:generate

**Make sure you set the correct database connection information before running the migrations**

    php artisan migrate
    php artisan serve

## Dependencies

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------

# Authentication

***SSO*** : Single Sign-On (SSO) is a method of authentication that allows a user to access multiple applications or systems with a single set of login credentials, such as a username and password. This eliminates the need for the user to remember multiple sets of login information and can improve security by reducing the number of times a user needs to enter sensitive information, such as a password.

- The SSO system will then authenticate the user and create a session, which is used to track the user's activity across multiple applications or systems. When the user attempts to access another application or system that is protected by SSO, the system will check the session to see if the user has already been authenticated and allow the user to access the application or system without requiring the user to enter login credentials again.

----------

# API Reference

## NOTE

### Create a new note

```http
  POST /api/notes/
  Content-Type: multipart/form-data
```

| Parameter | Type     | Description                                                      |
|:----------|:---------|:-----------------------------------------------------------------|
| `title`   | `string` | **Required**.  The title of the note **Length** min:3 max:100    |
| `content` | `string` | **Required**.  The content of the note **Length** min:3 max:3000 |
| `media`   | `mimes`  | **Optional**. jpg,jpeg,png,                                      |
| `tag`     | `string` | **Optional**.  The tag of the note                               |

### Update a note

```http
  POST /api/notes/{id}
  Content-Type: multipart/form-data
```

| Parameter | Type     | Description                                                      |
|:----------|:---------|:-----------------------------------------------------------------|
| `title`   | `string` | **Optional**.  The title of the note **Length** min:3 max:100    |
| `content` | `string` | **Optional**.  The content of the note **Length** min:3 max:3000 |
| `media`   | `mimes`  | **Optional**. jpg,jpeg,png                                       |
| `tag`     | `string` | **Optional**.  The tag of the note                               |

### Show a note

```http
  GET /api/notes/{id}
```

### Get a note items where have selected tag(id). To sort the records in descending order of created_at

```http
  GET /api/notes/tag/{id?}?from=0&offset=5
```

### Delete a note

```http
  DELETE /api/notes/{id}
```