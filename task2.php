<?php

/**
 * Script to download Wikipedia page, extract headings, abstracts, pictures, and links from sections,
 * and save the data in the wiki_sections table.
 *
 * @author Sushan Khatiwada
 * @version 1.0
 */

// Database connection configuration
$servername = "localhost";
$username = "username";
$password = "password";
$database = "database_name";

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // URL of the Wikipedia page to be scraped
    $url = "https://www.wikipedia.org/";

    // Initialize cURL session
    $ch = curl_init();
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute cURL session and store the HTML content
    $htmlContent = curl_exec($ch);

    // Create a DOMDocument object
    $dom = new DOMDocument();
    // Load the HTML content into the DOMDocument
    @$dom->loadHTML($htmlContent);

    // Get all sections with headings, abstracts, pictures, and links
    $sections = $dom->getElementsByTagName('section');
    foreach ($sections as $section) {
        // Extract data from the section (modify the code as needed based on the actual structure of the Wikipedia page)
        $heading = $section->getElementsByTagName('h2')->item(0)->textContent;
        $abstract = $section->getElementsByTagName('p')->item(0)->textContent;

        // Extract picture URL if available
        $pictureNode = $section->getElementsByTagName('img')->item(0);
        $picture = $pictureNode ? $pictureNode->getAttribute('src') : '';

        // Extract link URL if available
        $linkNode = $section->getElementsByTagName('a')->item(0);
        $link = $linkNode ? $linkNode->getAttribute('href') : '';

        // Save data into the database
        $stmt = $conn->prepare("INSERT INTO wiki_sections (date_created, title, url, picture, abstract) VALUES (NOW(), :title, :url, :picture, :abstract)");
        $stmt->bindParam(':title', $heading);
        $stmt->bindParam(':url', $link);
        $stmt->bindParam(':picture', $picture);
        $stmt->bindParam(':abstract', $abstract);
        $stmt->execute();
    }

    echo "Data inserted successfully\n";

    // Close cURL session and database connection
    curl_close($ch);
    $conn = null;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
