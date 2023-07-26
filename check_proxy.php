<?php
if (isset($_GET['proxy'])) {
    $proxy = $_GET['proxy'];
    $result = isProxyWorking($proxy);
    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => 'Proxy not provided.']);
}

function isProxyWorking($proxy) {
    $proxyUrl = 'http://' . $proxy;
    $testUrl = 'http://httpbin.org/ip'; // URL para verificar el proxy (solo HTTP)

    $options = array(
        'http' => array(
            'proxy' => $proxyUrl,
            'request_fulluri' => true,
        ),
    );

    $context = stream_context_create($options);
    $response = @file_get_contents($testUrl, false, $context);

    // Verificar si la respuesta contiene la dirección IP del proxy
    if ($response !== false) {
        $responseJson = json_decode($response, true);
        if (isset($responseJson['origin'])) {
            return true; // El proxy está funcionando correctamente
        }
    }

    return false; // El proxy no está funcionando o hay un error en la solicitud
}
