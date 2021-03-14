Before start, sorry if i'm not clear enough. It's my first documantation. 

# Simple-DB
Simple-DB is shorthand for simply select, insert, update and delete.

## Get Started
Just make sure you changed '$ConnectionConfig' variable for yourself. Nothing needed other than this.

Once you changed variable just call 'SDB("TableName")' for start. This function creates new instance of Simple_DB. But we will make everything through this function.

## Table($Columns, $Where)
If you want to get your all Table jus call this function through 'SDB("TableName")'

E.g.

`SDB("Classes")->Table();`

It will return associative array, something like this;

|Id|Grade|Branch|
|--|-----|------|
|12|11|A/T|
|13|11|A/T|
|14|11|B/L|
|15|9|C|
|16|12|B/L|

### Filtering
If you want to filter it's simple too.

For selecting spesific columns just add into $Columns parameter.

E.g.

`SDB("Classes")->Table("Grade, Branch");`

|Grade|Branch|
|-----|------|
|11|A/T|
|11|A/T|
|11|B/L|
|9|C|
|12|B/L|

!Note don't forget comma's on selecting multiple columns.

Using 'WHERE' statement is simple too;

"Columns/Values"

!Note use slash for seperate columns and values

E.g.

`SDB("Classes")->Table("", "Grade/11");`

will return;

|Id|Grade|Branch|
|--|-----|------|
|12|11|A/T|
|13|11|A/T|
|14|11|B/L|

or

`SDB("Classes")->Table("Grade, Branch", "Grade, Branch/12, B/L");`

will return;

|Grade|Branch|
|-----|------|
|11|B/L|
|12|B/L|

!Note as you can see there's no quotes at all. Just type the values.

## Insert($Values)
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
