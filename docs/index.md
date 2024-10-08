# PQL

PQL - PHP Query Language, Database Query Library

This library is a database abstraction layer which abstracts the query commands (Select, Delete, Update, etc.) out from the
drivers (MySQL, Postgres, SQL Server, etc.). Queries are defined in an eloquent way allowing you to
write almost all kinds of queries without having to rely on passing raw query data.

## Installation

Installation is done via composer `composer install arekxv/pql`

## Usage

First you decide on a database to be used. See Drivers for more information.

### Drivers

Following systems are supported:

* [MySQL](drivers/mysql.md) - MySQL database via PDO

## Testing

After installing the dependencies run `composer test`

For coverage report run `composer coverage` or you can take a look at it [here](https://scrutinizer-ci.com/g/ArekX/PQL/?branch=master).