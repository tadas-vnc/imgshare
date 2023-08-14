<?php 


    // Set the webhook URL or bot token
    $webhookUrl = "https://discord.com/api/webhooks/1118290111466049639/4wD0AW88zWCbkNzzx_sOvGclUNSuxIcShZmRTlKtDwNKBvor2wNIxPtL7uM5O6hi5qwc";
    $botToken = "insert token here";

    // Function to send the file to Discord
    function sendFileToDiscord($file, $webhookUrl = null, $botToken = null) {
        // Check if a webhook URL is provided
        if ($webhookUrl) {
            // Using a webhook
            $data = array(
                "content" => "New file uploaded!",
                "file" => new CURLFile($file['tmp_name'], $file['type'], $file['name'])
            );

            $ch = curl_init($webhookUrl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Parse the response to get the attachment URL
            $response = json_decode($response, true);
            if (isset($response['attachments'][0]['url'])) {
                return $response['attachments'][0]['url'];
            } else {
                return null;
            }
        } elseif ($botToken) {
            // Using a bot token
            $discordAPI = "https://discord.com/api/v10";
            $headers = array(
                "Authorization: Bot $botToken",
                "Content-Type: multipart/form-data"
            );

            $data = array(
                "content" => "New file uploaded!"
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $discordAPI . "/channels/1117892262983127101/messages");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // Parse the response to get the attachment URL
            $response = json_decode($response, true);
            if (isset($response['attachments'][0]['url'])) {
                return $response['attachments'][0]['url'];
            } else {
                return null;
            }
        } else {
            return null;
        }
		
    }
?>