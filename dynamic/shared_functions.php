<?php

/**
 * @file
 * Contains PHP functions shared by various pages.
 */

use GuzzleHttp\Client;

/**
 * Gets the text list.
 *
 * @return array
 *   The text list.
 */
function get_text_list($api) {
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
  return (array) json_decode(file_get_contents('cache/texts.json'));
}

/**
 * Gets a single text legend.
 *
 * @param string $id
 *   The identifier.
 *
 * @return array
 *   The single text.
 */
function get_text_legend($id, $api) {
  $cache_file = 'cache/legends/' . $id . '.txt';
  if (!file_exists($cache_file)) {
    $client = new Client();
    $res = $client->request('GET', $api . '?text=' . $id);
    if ($res->getStatusCode() == '200') {
      $file = fopen($cache_file, 'w');
      fwrite($file, $res->getBody());
      fclose($file);
    }
  }
  return (array) json_decode(file_get_contents($cache_file, 'w'));
}
