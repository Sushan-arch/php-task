<?php

/**
 * Script to find and display files in the /datafiles folder with names consisting of numbers
 * and letters of the Latin alphabet and having the .ixt extension, ordered by name.
 *
 * @author Sushan Khatiwada
 * @version 1.0
 */

// Define the folder path
$folderPath = '/datafiles';

// Use a callback function to filter files during directory traversal
$matchingFiles = array_filter(scandir($folderPath), function ($file) {
    return preg_match('/^[a-zA-Z0-9]+\.ixt$/', $file);
});

// Sort the matching files by name
sort($matchingFiles);

// Display the names of matching files
echo "Matching files:\n";
foreach ($matchingFiles as $file) {
    echo $file . "\n";
}
