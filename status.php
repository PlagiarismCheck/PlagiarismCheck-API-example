<?php

require 'config.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('id is empty');
    }
    $id = \intval($_POST['id']);

    $curl = \curl_init();
    \curl_setopt_array(
        $curl,
        [
            CURLOPT_URL => 'https://plagiarismcheck.org/api/v1/text/'.$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => false,
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
        if ($json->data && $json->data->state === 5) {
            $response = [
                'success' => true,
                'checked' => true,
            ];
        } elseif (\in_array($json->data->state, [2, 3], true)) {
            $response = [
                'success' => true,
                'checked' => false,
            ];
        } else {
            $response = [
                'success' => false,
                'checked' => false,
            ];
        }

    }
} catch
(\Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
} finally {
    \curl_close($curl);
}


echo \json_encode($response);
