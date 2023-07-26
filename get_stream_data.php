<?php
function checkStream($channelName, $proxy, $url) {
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

    $mpd_contents = file_get_contents($url);

    $xml = new SimpleXMLElement($mpd_contents);

    $options = array();

    foreach ($xml->Period->AdaptationSet as $adaptationSet) {
        $mimeType = (string)$adaptationSet['mimeType'];

        if ($mimeType == 'video/mp4' || $mimeType == 'audio/mp4' || $mimeType == 'text/vtt') {
            $type = ($mimeType == 'video/mp4') ? 'video' : (($mimeType == 'audio/mp4') ? 'audio' : 'subtitle');

            $options[$type] = array();

            $language = (string)$adaptationSet['lang'];

            foreach ($adaptationSet->Representation as $representation) {
                $option = array(
                    'id' => (string)$representation['id'],
                    'mimeType' => $mimeType,
                    'width' => (string)$representation['width'],
                    'height' => (string)$representation['height'],
                    'sampleRate' => (string)$representation['sampleRate'],
                    'language' => $language,
                );

                $options[$type][] = $option;
            }
        }
    }

    return $options;
}

if (isset($_GET['channelId']) && !empty($_GET['channelId'])) {
    $channelId = $_GET['channelId'];
    $channel = getChannelById($db, $channelId);
    if ($channel) {
        $channelName = $channel['name'];
        $proxy = $channel['proxy'];
        $url = $channel['url'];
        $streamData = checkStream($channelName, $proxy, $url);

        header('Content-Type: application/json');
        echo json_encode($streamData);
        exit;
    }
}
?>
