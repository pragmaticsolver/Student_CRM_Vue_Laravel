<?php

namespace App\Helpers;

use App\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ZoomClient {

    var $jwt_token;

    public function __construct()
    {
        // Generate JWT Token
        $this->jwt_token = $this->generateJWT();
    }

    private function generateJWT()
    {
        $api_key = Settings::get_value('zoom_api_key');
        $api_secret = Settings::get_value('zoom_secret_key');

        // Create the token header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        // Create the token payload
        $payload = json_encode([
            'iss' => $api_key,
            'exp' => (time() + 60 )* 1000 // Setting expiration time to 60 second
        ]);

        // Encode Header
        $base64UrlHeader = CommonHelper::base64UrlEncode($header);

        // Encode Payload
        $base64UrlPayload = CommonHelper::base64UrlEncode($payload);

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $api_secret, true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = CommonHelper::base64UrlEncode($signature);

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    public function getUserSettings($user_email)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $this->jwt_token];
            $client = new Client();
            $response = $client->request('GET', 'https://api.zoom.us/v2/users/' . $user_email .'/settings', [
                'headers' => $headers,
            ]);
            $userSettings = json_decode($response->getBody()->getContents(), 1);
            
            $out['status'] = 1;
            $out['user_settings'] = $userSettings;
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function createMeeting($user_email, $body)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $this->jwt_token];
            $client = new Client();
            $response = $client->request('POST', 'https://api.zoom.us/v2/users/' . $user_email .'/meetings', [
                'headers' => $headers,
                'json' => $body
            ]);
            
            $meeting = json_decode($response->getBody()->getContents(), 1);

            $out['status'] = 1;
            $out['meeting'] = $meeting;
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function deleteMeeting($meeting_id)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $this->jwt_token];
            $client = new Client();
            $response = $client->request('DELETE', 'https://api.zoom.us/v2/meetings/' . $meeting_id, [
                'headers' => $headers,
            ]);

            $out['status'] = 1;
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function getUser($user_email)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $this->jwt_token];
            $client = new Client();
            $response = $client->request('GET', 'https://api.zoom.us/v2/users/' . $user_email, [
                'headers' => $headers,
            ]);

            $out['status'] = 1;
            $out['user'] = json_decode($response->getBody()->getContents(), 1);
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function getMeeting($meeting_id)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $this->jwt_token];
            $client = new Client();
            $response = $client->request('GET', 'https://api.zoom.us/v2/meetings/' . $meeting_id, [
                'headers' => $headers,
            ]);

            $out['status'] = 1;
            $out['meeting'] = json_decode($response->getBody()->getContents(), 1);
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    private function handleError($e) 
    {
        \Log::error($e);

        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();            
        $response = json_decode($responseBodyAsString,1);

        $out['status'] = 0;
        $out['code'] = $response['code'];
        $out['message'] =  __('messages.zoom-api-error'). ": " . $response['message'];
        return $out;
    }
}

?>