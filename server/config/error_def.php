<?php
return array(
    //memcache error
    101 => 'Memcache is not currently installed...',
    103 => 'Could not connect to the Memcache host',

	//mongodb error
	201 => 'The MongoDB PECL extension has not been installed or enabled',
	203 => 'To switch MongoDB databases, a new database name must be specified',
	205 => 'Unable to switch Mongo Databases',
	207 => 'In order to retreive documents from MongoDB, a collection name must be passed',
	209 => 'In order to retreive a count of documents from MongoDB, a collection name must be passed',
	211 => 'No Mongo collection selected to insert into',
	213 => 'Nothing to insert into Mongo collection or insert is not an array',
	215 => 'Insert of data into MongoDB failed',
	217 => 'No Mongo collection selected to update',
	219 => 'Nothing to update in Mongo collection or update is not an array',
	221 => 'Update of data into MongoDB failed',
	223 => 'No Mongo collection selected to delete from',
	225 => 'Delete of data into MongoDB failed',
	227 => 'No Mongo collection specified to add index to',
	229 => 'Index could not be created to MongoDB Collection because no keys were specified',
	231 => 'An error occured when trying to add an index to MongoDB Collection',
	233 => 'No Mongo collection specified to remove index from',
	235 => 'Index could not be removed from MongoDB Collection because no keys were specified',
	237 => 'An error occured when trying to remove an index from MongoDB Collection',
	239 => 'Unable to connect to MongoDB',
	241 => 'The Host must be set to connect to MongoDB',
	243 => 'The Database must be set to connect to MongoDB',
	
	//mysql error
	301 => 'Can\'t pConnect MySQL Server',
	303 => 'MySQL Query Error',
	305 => 'Can\'t select MySQL database'
);