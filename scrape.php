<?php
  $url = "https://api.scraperapi.com/structured/amazon/search?api_key=babe742a0e34723b70cb4d5b316ca2d3&query=Rising%20while%20falling%20down&country_code=us&tld=com";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $response = curl_exec($ch);
  curl_close($ch);
  print_r($response);
  ?>
