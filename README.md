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

* Design







## Usage examples







## File Structure

use tree command.





## Frameworks

### Routing

* slim v4

Official documentation: https://www.slimframework.com/docs/v4/

### ORM

* Cycle ORM

Official documentation: https://cycle-orm.dev/docs







## Test tools

* postman

I use postman to design and test the RESTful API, and the APIs are exported to a .json file. You can check it by loading it into postman.



## Future improvements

