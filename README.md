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

use tree command. tree -I 'vendor'





## Frameworks

### Routing

* slim v4

Official documentation: https://www.slimframework.com/docs/v4/




## Test

* Unit test

The unit test folder is tests/ which is already configured in file phpunit.xml.
 And we can run the unit test just with the command `./vendor/bin/phpunit`.


## Future improvements

* Use ORM framework

It will take some time for me to use some ORM framework like Laravel/orm or Doctrine well.
But the use of ORM will grant the project more scalability.

* Paginating Query Results 

For messages, if there are many records, we can set pagination query and set 100 limit for each query.
Also use resumeToken to concat the results. More general solution is to build a long connection such 
as WebSocket connection between the user and the group server, and server with real-time message stream.
