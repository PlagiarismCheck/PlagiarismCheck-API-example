<?php

require 'config.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['content'])) {
        throw new Exception('Content is empty');
    }
    $content = $_POST['content'];
    $postData = [
        'language' => 'en',
        'text' => $content,
    ];
    $requestData = [];
    foreach ($postData as $name => $value) {
        $requestData[] = $name.'='.urlencode($value);
    }

    $curl = \curl_init();
    \curl_setopt_array(
        $curl,
        [
            CURLOPT_URL => 'https://plagiarismcheck.org/api/v1/text',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => \implode('&', $requestData),
            CURLOPT_HTTPHEADER => array(
                'X-API-TOKEN: '.TOKEN,
            ),
        ]
    );
    if (!$curl) {
        throw new Exception(\curl_error($curl));
    }

    if ($apiResponse = \curl_exec($curl)) {
        $json = \json_decode($apiResponse);
        if (!$json) {
            throw new Exception('Can not parse JSON');
        }
        if ($json->success) {
            $response = [
                'success' => true,
                'id' => $json->data->text->id,
            ];
        } else {
            $response = [
                'success' => false,
                'error' => $json->message,
            ];
        }


    }


} catch (\Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
} finally {
    \curl_close($curl);
}


echo \json_encode($response);
