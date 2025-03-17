# demo-group-chat

## Introduction

A chat application backend in PHP.

### Features

* users are able to create chat groups;
* users are able to join any group;
* users are able to send messages within groups;
* users are able to list all the messages within a group;
* The data should be stored in a simple SQLite database;

### Design

* Requirement

All communication between the client and server should happen over a simple RESTful JSON API over HTTP(s) (which may be periodically refreshed to poll for new messages). A GUI, user registration and user login are not needed, but the users should be identified by some token, username or ID in the HTTP messages and the database.

* Table Design

I create four tables based on the simplified requirement, and use `user`, `group`, `message` to store the content of these object. I also use a table `user_group`, to store the information 
whether the user is in the group for the functionality to check  user already join the group and can see the messages in the group. If the user is not in the group, then he/she will not 
have the permission to see the messages in the group. For simplification, I don't set the Foreign Key of user_id and group_id.

```sqlite
CREATE TABLE IF NOT EXISTS user (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            username   TEXT UNIQUE NOT NULL,
            token      TEXT UNIQUE NOT NULL
        );

CREATE TABLE IF NOT EXISTS `group` (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            groupname       TEXT UNIQUE NOT NULL,
            create_user_id  INTEGER NOT NULL,
            created_at      TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
CREATE TABLE IF NOT EXISTS message (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER NOT NULL,
            group_id    INTEGER NOT NULL,
            content     TEXT NOT NULL,
            created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
CREATE TABLE IF NOT EXISTS user_group (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER NOT NULL,
            group_id    INTEGER NOT NULL,
            created_at  TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT unq UNIQUE (user_id, group_id)
        );
```







## Usage examples

Enter the root dir, and run the following command.

```bash
# Init the vendor
composer install
# Start the server
php -S localhost:8081 -t public/ 
# I also add a start command in the composer.json, you can just use start to simplify the command
composer start

```



## File Structure

use tree command. tree -I 'vendor'

``` markdown
.
├── LICENSE
├── README.md
├── chat.db            // the sqlite db file, will created when you run the server
├── composer.json      // dependency manage file
├── composer.lock
├── config
│   ├── bootstrap.php   // start point
│   ├── container.php   // container configuration
│   ├── middleware.php  // set up middleware logs for all routes
│   ├── routes.php      // register routes
│   └── settings.php    // general setting (errors)
├── docs
│   └── groupChat.postman_collection.json   // API documemtations (for test) of postman
├── public
│   └── index.php
└── src
    ├── DB                    // initialize database and tables
    │   └── init.php
    ├── Middleware            // set up Auth middleware for specific API
    │   └── AuthMiddleware.php
    └── Service               // business logic
        ├── GroupService.php
        ├── MessageService.php
        └── UserService.php
```

## Frameworks

### Routing

* slim v4

Official documentation: https://www.slimframework.com/docs/v4/




## Test

* Unit test

It looks like no complicated functions that need to do the unit test. All the function should 
work well only by the integration test.

* Integration test

I use Postman to execute the integration tests. I already export the API documentation
 as .json file and store it in the folder `./docs/`. You can also import them into Postman 
and test the APIs.


## Future improvements

* Use ORM framework

It will take some time for me to learn and use some ORM framework like **Laravel/orm** or **Doctrine** well.
But the use of ORM will grant the project more scalability.

* Paginating Query Results 

For messages, if there are many records, we can set pagination query and set 100 limit for each query.
Also use resumeToken to concat the results. More general solution is to build a long connection such 
as WebSocket connection between the user and the group server, and server with real-time message stream.

* Automation integration test

There are standard ways to use **postman** and **newman** to develop the automation integration test for APIs. 
I once developed a simple automation test tool with **newman** and **gitlab**. I also share part of the solution on 
my [personal blog](https://infinityjay.github.io/ci/cd/Gitlab-CI-use-mysql-service/). Due to time and 
environment limitations, I do not develop such testing tools here.

