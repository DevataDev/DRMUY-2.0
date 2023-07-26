<?php
function checkPermission($path, $permission)
{
    if (file_exists($path)) {
        $actualPermission = substr(sprintf('%o', fileperms($path)), -4);
        return $actualPermission == $permission;
    }
    return false;
}

$filesAndPermissions = [
    'N_m3u8DL-RE' => '0777',
    'mkfifo' => '0777',
    'packager-linux-x64' => '0777',
    'ffprobe' => '0777',
    'ffmpeg' => '0777',
];

$directoriesAndPermissions = [
    'temp' => '0755',
    'Logs' => '0755',
    'm3u8' => '0755',
];

$result = '<h1 class="font-bold text-xl mb-2">File Permissions Test</h1>';

$result .= '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">';
$result .= '<h2 class="font-bold text-lg mb-2">Files:</h2>';
foreach ($filesAndPermissions as $file => $permission) {
    if (checkPermission('../bin/' . $file, $permission)) {
        $result .= "<p>$file - $permission (Allowed)</p>";
    } else {
        $result .= "<p>$file - $permission (Not Allowed)</p>";
    }
}
$result .= '</div>';

$result .= '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">';
$result .= '<h2 class="font-bold text-lg mb-2">Directories:</h2>';
foreach ($directoriesAndPermissions as $directory => $permission) {
    if (checkPermission('../bin/' . $directory, $permission)) {
        $result .= "<p>$directory - $permission (Allowed)</p>";
    } else {
        $result .= "<p>$directory - $permission (Not Allowed)</p>";
    }
}
$result .= '</div>';

echo $result;
?>
