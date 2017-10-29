<?php

/**
 * @file
 * Lists available texts.
 */

use GuzzleHttp\Client;

$texts = [];
if (!file_exists('cache/texts.json')) {
  $client = new Client();
  $res = $client->request('GET', $api . '?get_texts=1');
  if ($res->getStatusCode() == '200') {
    $file = fopen("cache/texts.json", "w");
    fwrite($file, $res->getBody());
    fclose($file);
  }
}
$texts = (array) json_decode(file_get_contents('cache/texts.json'));
$allowed = (array) json_decode(file_get_contents('allowed.json'));

if ($texts) {
  $rows = [];
  $genres = [];
  foreach ($texts as $id => $values) {
    if (in_array($id, $allowed)) {
      // This first if statement is only to support legacy
      // text listing. It can be removed after verifying update.
      if (is_string($values)) {
        $rows[$id]['title'] = '<a href="/selection/' . $id . '">' . $values . '</a>';
        $rows[$id]['genre'] = '';
        $rows[$id]['wordcount'] = '';
      }
      else {
        $rows[$id]['title'] = $values->title;
        $rows[$id]['link'] = '<a href="/selection/' . $id . '">' . $values->title . '</a>';
        $rows[$id]['genre'] = $values->genre;
        $rows[$id]['wordcount'] = $values->wordcount;
        $genrekey = strtolower(str_replace(' ', '-', $values->genre));
        $genres[$genrekey] = $values->genre;
      }
    }
  }
}

if (!empty($rows)) {
  echo '<h3>Available Texts</h3>';
  // Genre selection.
  if (!empty($genres)) {
    echo '<form action="/text_list" method="get">';
    echo '<label for="bygenre">View by genre</label>';
    echo '<select name="bygenre">';
    foreach ($genres as $key => $genre) {
      echo '<option value="' . $key . '"';
      if (isset($_GET['bygenre'])) {
        if ($_GET['bygenre'] == $key) {
          echo ' selected="selected"';
        }
      }
      echo '>' . $genre . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" name="submit" value="Filter" />';
    echo '</form>';
  }
  if (isset($_GET['bygenre']) && in_array($_GET['bygenre'], array_keys($genres))) {
    foreach ($rows as $id => $row) {
      $thisgenre = $_GET['bygenre'];
      if ($row['genre'] != $genres[$thisgenre]) {
        unset($rows[$id]);
      }
    }
  }
  // Sort functionality:
  if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'title') {
      usort($rows, function ($a, $b) {
        return strcmp($a["title"], $b["title"]);
      });
    }
    if ($_GET['sort'] == 'genre') {
      usort($rows, function ($a, $b) {
        return strcmp($a["genre"], $b["genre"]);
      });
    }
    if ($_GET['sort'] == 'words') {
      usort($rows, function ($a, $b) {
          return $a['wordcount'] - $b['wordcount'];
      });
    }
  }
  $queries = isset($_GET['bygenre']) ? '&bygenre=' . $_GET['bygenre'] : '';
  echo '<table>';
  echo '<thead><th><a href="/text_list?sort=title' . $queries . '">Title</a></th><th><a href="/text_list?sort=genre' . $queries . '">Genre</a></th><th><a href="/text_list?sort=words' . $queries . '">Word count</a></th></thead>';
  foreach ($rows as $id => $row) {
    echo '<tr><td>' . $row['link'] . '</td><td>' . $row['genre'] . '</td><td>' . number_format($row['wordcount']) . '</td></tr>';
  }
  echo '</table>';
}
