!! This project was a experimenting of jQuery style chaining also testing my 'skills'. Not safe to use.

Before start, sorry if i'm not clear enough. It's my first documantation. 

# Get Started
## Setup
It's not big deal to setup Simple-DB.

Download Simple-DB and include to your project.

To connect your database you have two choice;

### Set with `SDB()`
First one is giving an associative array to `SDB()`.

`SDB(["Host"=>"localhost", "Username"=>"root", "Password"=>"passw", "Database"=>"somedb"]);`

### Array as global
Second one is defining same array as global variable. Just name it 'SimpleDB_ConnectConf' your variable and assign values.

```
$SimpleDB_ConnectConf = 
[
    "Host"=>"localhost",
    "Username"=>"root",
    "Password"=>"passw",
    "Database"=>"somedb"
];
```

## Syntax
Syntax based on CSV. For example;

`"Id, Content/1, Somethings come up."`

Alway columns left side of the slash and values are in the right. Everything separated by comma. But what if we have value with a comma? Than use single quotes to point "that a single value man!"

`"Id, Content/1, 'Somethings, come up'"`

## Usage
We will make everything through `SDB();` function.

For selecting table give name to it. For example;

`SDB("Users")->...`

Just like that. It will search for a table. If table doesn't exist than you will get error.

# Fetching Data
For get data in our hands we have plenty ways to do it.

* Table
* Row
* Search

## Table($Columns, $Where)
Table function returns associative array of your table. Optionally have two parameters to pass.

Example usage;

`SDB("Users")->Table();`

### $Columns
If you want to fetch one or two or maybe more columns pass a string that includes column names. If you not want to use just leave it empty.

Example usage;

`SDB("Users")->Table("FirstName, LastName");`

If sdb can't find columns it will throw error.

### $Where
For fetching specified rows pass 'Columns/Values' to this parameter.

Example usage;

`SDB("Users")->Table("", "FirstName, LastName/John, Doe");`

## Row($Columns, $Where)
When you need only one row this function meet your needs. It's same as Table.

Example usage;

`SDB("Users")->Row("FirstName, LastName", "Id/1");`

## Search($Search, $In)
Need a simple search method? Search() why stands for.

Example usage;

`SDB("Users")->Search("John", "FirstName, LastName");`

### $Search
This is what are you looking for. Like a query but just a string.

### $In
It's actually columns. If you want search in specified columns pass columns to this. By the default it's searches in all columns.

# Insert($Values)
For insterting data into table just do the same thing like where statement.

E.g

`SDB("Classes")->Insert("Grade, Branch/9, B/L");`

Table will look like this;

|Id|Grade|Branch|
|--|-----|------|
|12|11|A/T|
|13|11|A/T|
|14|11|B/L|
|15|9|C|
|16|12|B/L|
|17|9|B/L|

or

`SDB("Classes")->Insert("Grade/12");`

Table will look like this;

|Id|Grade|Branch|
|--|-----|------|
|12|11|A/T|
|13|11|A/T|
|14|11|B/L|
|15|9|C|
|16|12|B/L|
|17|12||

This function returns true on success and false on error.

## Update($Set, $Where)
For updating table we'r gonna use same thing again.

E.g

`SDB("Classes")->Update("Grade, Branch/11, A/T", "Id/14");`

After that call our table changed like this;

|Id|Grade|Branch|--|
|--|-----|------|--|
|12|11|A/T||
|13|11|A/T||
|14|11|A/T|*|
|15|9|C||
|16|12|B/L||

This function returns true on success and false on error.

## Delete($Where)
And the last thing you should know is deleting. Like the others it's same too. Select table, find and do the process.

E.g

`SDB("Classes")->Delete("Id/12");`

After that call our table changed like this;

|Id|Grade|Branch|
|--|-----|------|
|13|11|A/T|
|14|11|B/L|
|15|9|C|
|16|12|B/L|

This function returns true on success and false on error.

***
I hope you get the point :)
