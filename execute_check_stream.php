<?php
if (isset($_GET['channelId']) && !empty($_GET['channelId'])) {
    $channelId = $_GET['channelId'];
    $channel = getChannelById($db, $channelId);
    if ($channel) {
        $channelName = $channel['name'];
        $proxy = $channel['proxy'];
        $url = $channel['url'];
        $useProxy = ($channel['useProxy'] == 'true');

        if ($useProxy && isProxyWorking($proxy)) {
            $proxyURL = $proxy;
        } else {
            $proxyURL = null;
        }

        $streamData = getStreamData($url, $proxyURL);

        echo json_encode(['success' => true, 'data' => $streamData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Channel not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Channel ID not provided.']);
}

function getStreamData($url, $proxyURL) {
    $options = array();

    if ($proxyURL !== null) {
        $command = "./bin/yt-dlp --list-formats --allow-unplayable-formats --proxy $proxyURL $url";
    } else {
        $command = "./bin/yt-dlp --list-formats --allow-unplayable-formats $url";
    }

    $output = shell_exec($command);

    $matches = [];
    $pattern = '/(\w+)\s+(\w+)\s+(\w+)\s+(\w+)\s+\|\s+(\d+k)\s+(dash)/';
    preg_match_all($pattern, $output, $matches, PREG_SET_ORDER);

    if (!empty($matches)) {
        foreach ($matches as $match) {
            $option = array(
                'id' => $match[1],
                'mimeType' => $match[2],
                'width' => $match[3],
                'height' => $match[4],
                'sampleRate' => $match[5],
            );

            $options[] = $option;
        }
    }

    return $options;
}
?>
