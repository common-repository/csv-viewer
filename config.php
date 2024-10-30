<?php
define(CSV_UPLOAD_DIR, 'csv-viewer');
define(CSV_UPLOAD_PATH, CSV_BASEDIR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.CSV_UPLOAD_DIR.DIRECTORY_SEPARATOR);
define(CSV_FILE_MAXIMUM_SIZE, 2000000); //2MB
define(CSV_FILE_MAXIMUM_ROWS, 200);
define(CSV_TABLENAME, 'csv-viewer');

$csv_allowed_extensions = array('csv');