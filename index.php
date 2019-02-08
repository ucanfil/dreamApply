<?php

  // $ch = curl_init();
  // $url = 'https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml';
  // curl_setopt( $ch, CURLOPT_URL, $url);
  // curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

  // $content = curl_exec($ch);
  // $xml=simplexml_load_string($content);
 
  // var_dump($xml);

  
  $xml = simplexml_load_file('https://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml');

  foreach ($xml->territoryInfo->territory as $row) {
    $population = $row->attributes()['population'];
    $type = isset($row->languagePopulation) ? '' . $row->languagePopulation->attributes()['type'] : null;
    $populationPercent = isset($row->languagePopulation) ? $row->languagePopulation->attributes()['populationPercent'] : null;
    $totalPerson = round($populationPercent * $population * 0.01);
    $lang['type'] = $type;
    $lang['totalPerson'] = $totalPerson;
    $data[] = $lang;
  }

  foreach ($data as $d) {
    if (empty($tully[$d['type']])) {
      $tully[$d['type']] = $d['totalPerson'];
    } else {
      $tully[$d['type']] += $d['totalPerson'];
    }
  }

  arsort($tully);
  foreach ($tully as $key => $value) {
    echo $key . ' ' . $value . "\n";
  }