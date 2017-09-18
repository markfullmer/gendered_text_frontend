<?php

include '../vendor/autoload.php';
use \PHPePub\Core\EPub;

error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

if (!isset($_GET['export']) || !(isset($_GET['title']))) {
  die();
}
$cache_file = '../cache/text/' . $_GET['export'] . '.txt';
if (file_exists($cache_file)) {
  $text = file_get_contents($cache_file);
  $title = urldecode($_GET['title']);
}
else {
  die();
}

$content_start =
"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
. "<head>"
. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";
$bookEnd = "</body>\n</html>\n";
// setting timezone for time functions used for logging to work properly
date_default_timezone_set('America/Chicago');
$fileDir = './PHPePub';
$book = new EPub(); // no arguments gives us the default ePub 2, lang=en and dir="ltr"
// Title and Identifier are mandatory!
$book->setTitle($title);
$book->setIdentifier("https://github.com/markfullmer/gendered_text", EPub::IDENTIFIER_URI); // Could also be the ISBN number, preferrd for published books, or a UUID.
$book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("This book was generated from the Gendered Text Project");
$book->setAuthor('Emily Parkhurst', 'Mark Fullmer');
$book->setPublisher('Emily Parkhurst & Mark Fullmer', "https://github.com/markfullmer/gendered_text");
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book.");
$book->setSourceURL("https://github.com/markfullmer/gendered_text");

// A book needs styling.
$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

// Add cover page.
$cover = $content_start . '<h1>' . $title . '</h1>' . $bookEnd;
$book->addChapter("Notices", "Cover.html", $cover);
$chapter1 = $content_start . $text . $bookEnd;
$book->addChapter("Book Content", "Chapter001.html", $chapter1, TRUE, EPub::EXTERNAL_REF_ADD);

$book->finalize(); // Finalize the book, and build the archive.
// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook($title);
