<?php
$command = './bin/yt-dlp --list-formats --allow-unplayable-formats --proxy http://167.60.143.54:808 https://dce-ac-live.cdn.indazn.com/dash/dazn-linear-017/stream.mpd';
$output = shell_exec($command);

$matches = [];
$pattern = '/(\w+)\s+(\w+)\s+(\w+)\s+(\w+)\s+\|\s+(\d+k)\s+(dash)/';
preg_match_all($pattern, $output, $matches, PREG_SET_ORDER);

if (!empty($matches)) {
    echo "<pre>";
    echo "ID\t\tEXT\t\tRESOLUTION\tTBR\n";
    foreach ($matches as $match) {
        echo "{$match[1]}\t{$match[2]}\t{$match[3]}\t{$match[5]}\n";
    }
    echo "</pre>";
} else {
    echo "No se encontraron datos disponibles.";
}
?>