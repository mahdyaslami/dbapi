README
======

Prerequisites
-------------

1. The **PRIMARY KEY** to all the tables have to be the name of the 'id'.

Error Handling
--------------

1. **STATUS** code of response have to change with content. [See error codes](doc/http-errors.md)

2. **Error Message** have to send if there is or not. 

    Empty response mean the result is emtpy array of results.

    if response code is 204 it mean database has returned false value.