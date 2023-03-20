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
            CURLOPT_URL => 'https://plagiarismcheck.org/api/v1/text/report/'.$id,
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
        if ($json->data && $json->data->report) {
            $response = [
                'success' => true,
                'percent' => $json->data->report->percent,
            ];
        } else {
            $response = [
                'success' => false,
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
