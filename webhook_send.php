<?php function sendDiscordWebhook($message, $url) {
    $data = array(
        'content' => $message
    );

    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}
?>