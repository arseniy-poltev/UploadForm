
There are 30+ different documents (eg. clients, vendors, sales order, purchases orders,..) that uses should be able to upload to a database. Actually two database:
Database 1: Creat a new table based on whatever is in the document. Query all columns and create the table based on a configured variable
Database 2: That one is fixed with defined colums, formats and name. Content of the document shall be uploaded as best as possible

--> Each document has other columns, another name and needs to be uploaded in other destination tables within database 2. 
--> For each document APIs should be used. I want to configure per document (the 30+) which APIs (Array) i will use. APIs are stored in  database table
--> I don't want 30+ individual php upload scripts, but one very flexible script that calls functions. I call it the "template"
--> The content of the fies should be uploaded A) into the database "1_raw_data" and 2) in the database "2_transform_data"

Example in pseudo code of the "template"
*/
//Configuration of APIS
--> apis are stored in the database and have the full url+api key + fields
--> Fields in  the apis database are: ID | api_url | api_key | s_fields (array) | r_fields (array)

// overall config
--> The user should be able to select on uploading whether he wants to overwrite all in 2_transform_data or append datasets --> to be stored in array of variable $config (eg. overwrite:yes|no)
--> more configuration in the future.
*/ 

function uploadload_to_db_1 ($sourcefile,$targetfile){
1) read the $sourcefile  and store the column headers and content seperatly
2) Check if a table of the name of $sourcefile does already existing in database "1_raw_data". If yes, delete it
3) Create a new table in database "1_raw_data" using the headers of the $sourcefile and the content of the $sourcefile
}

function upload_to_db_2 ($table,$mappingtable,$sourcefile,$api,$config){
1) read the $sourcefile and store the column headers and content seperatly
2) read the structure of $table and store the headers
3) Check if the headers of the $sourcefile match those of the $table.
    --> Use the names and see if you have columns of the same name
    --> Use $mappingtable which is a table of two columns saying column-header A (in the csv) map to column-header 1 (in the $table)
4) Return to the user a mapping table frontend. The user must be able to adjust the mapping (step 3). The user should be able to save the mapping  table to $mappingtable
5) Store the $sourcefile in the path of $targetfile
6) APIs (very complicated!)
    --> in $api you find array of apis (ids) -> from the database see line 11 in this file! The urls look like https://maps.googleapis.com/maps/api/geocode/json?address=$a_street+$a_city+$a_countrykey=$apikey
    --> The apis should be used on the $sourcefile content but have the field names of the $table. You need to use the $mappingtable!
    --> There should be a loop through the whole array of api. All Apis will send responses in json format
    --> Use the documented r_fields from the apis database to understand what fields to be used from the api response
    --> Store the reponses 
7) Upload to 2_transform_data in table $table
    --> Check if the table exists
    --> Check if the table is empty --> if not check $config[overwrite]. Truncate table if overwrite:yes
    --> Append/insert content of $sourcefile + responses from the API to 2_transform_data.$table
}

Call function (examples):
upload_to_db_1 ($_FILES['data']['name']),'/www/targetfolder1/sales/');
upload_to_db_2 ('m_sales_region',$_FILES['mapping_table_user1']['name']),'20,21,22,23,30','overwrite:yes')

--> For some of the scripts (30+) there are more functions needed to check if data is already in the another table in 2_transform_data.
    -->Example: If you upload a list of customers you need to check if the customer is already in the database
    -->Example: If you upload sales orders, you need to check if in the table customers a customer of the same name exists and store its ID  to the sales order table instead of the name of the customer(relational database)