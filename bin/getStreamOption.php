<?php


$proxy = 'tcp://167.60.133.115:808';
$proxy_context = array(
    'http' => array(
        'proxy' => $proxy,
        'request_fulluri' => true,
    ),
    'https' => array(
        'proxy' => $proxy,
        'request_fulluri' => true,
    ),
);
stream_context_set_default($proxy_context);

$url = 'https://dtvott-abc.akamaized.net/dash_live_1256/manifest.mpd';
$mpd_contents = file_get_contents($url);

$xml = new SimpleXMLElement($mpd_contents);

$options = array();

foreach ($xml->Period->AdaptationSet as $adaptationSet) {
    $mimeType = (string)$adaptationSet['mimeType'];

    if ($mimeType == 'video/mp4' || $mimeType == 'audio/mp4') {
        $type = ($mimeType == 'video/mp4') ? 'video' : 'audio';

        $options[$type] = array();

        foreach ($adaptationSet->Representation as $representation) {
            $option = array(
                'id' => (string)$representation['id'],
                'mimeType' => $mimeType,
                'width' => (string)$representation['width'],
                'height' => (string)$representation['height'],
                'sampleRate' => (string)$representation['sampleRate']
            );

            $options[$type][] = $option;
        }
    }
}

$jsonData = json_encode($options, JSON_PRETTY_PRINT);

// Save the JSON data to a file
$file = 'mpd_options.json';
file_put_contents($file, $jsonData);
