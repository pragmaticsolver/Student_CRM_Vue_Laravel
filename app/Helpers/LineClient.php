<?php

namespace App\Helpers;

use App\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Jose\Component\Core\JWK;
use Jose\Easy\Build;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\Algorithm\RS256;
class LineClient
{
    public function getChannelAccessTokens()
    {
        try {
            $query = [
               'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
               'client_assertion' => $this->generateJWT()
            ];

            $client = new Client();
            $response = $client->request('GET', 'https://api.line.me/oauth2/v2.1/tokens', [
                'query' => $query
            ]);

            $out['status'] = 1;
            $out['access_tokens'] = json_decode($response->getBody()->getContents(), 1)['access_tokens'];
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function issueChannelAccessToken()
    {
        try {
            $form_params = [
               'grant_type' => 'client_credentials',
               'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
               'client_assertion' => $this->generateJWT()
            ];

            $client = new Client();
            $response = $client->request('POST', 'https://api.line.me/oauth2/v2.1/token', [
                'form_params' => $form_params
            ]);

            $res = json_decode($response->getBody()->getContents(), 1);
            $out['status'] = 1;
            $out['access_token'] = $res['access_token'];
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    // For line login
    public function getAccessToken($authorization_code)
    {
        try {
            $form_params = [
               'grant_type' => 'authorization_code',
               'code' => $authorization_code,
               'redirect_uri' => route('login.line.callback'),
               'client_id' => Settings::get_value('line_login_channel_id'),
               'client_secret' => Settings::get_value('line_login_channel_secret')
            ];

            $client = new Client();
            $response = $client->request('POST', 'https://api.line.me/oauth2/v2.1/token', [
                'form_params' => $form_params
            ]);

            $res = json_decode($response->getBody()->getContents(), 1);
            $out['status'] = 1;
            $out['access_token'] = $res['access_token'];
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    public function getUserProfile($access_token)
    {
        try {
            $headers = ['Authorization' => 'Bearer ' . $access_token];

            $client = new Client();
            $response = $client->request('GET', 'https://api.line.me/v2/profile', [
                'headers' => $headers,
            ]);

            $res = json_decode($response->getBody()->getContents(), 1);
            
            $out['status'] = 1;
            $out['userId'] = $res['userId'];
            $out['displayName'] = $res['displayName'];
            return $out;
        } catch (ClientException $e) {
            return $this->handleError($e);
        }
    }

    private function generateJWT()
    {
        new AlgorithmManager([ new RS256() ]);

        $channel_id = Settings::get_value('line_channel_id');
        $private_key_json = Settings::get_value('line_assertion_private_key');
        $private_key = json_decode($private_key_json, 1)['privateKey'];

        $jwk = new JWK($private_key);
        $jws = Build::jws()
            ->claim('iss', $channel_id)
            ->claim('sub', $channel_id)
            ->claim('aud', "https://api.line.me/")
            ->claim('exp', time() + (30 * 60)) // expiration time of jwt token 60 sec from time of creation
            ->claim('token_exp', ( 86400 * 30) ) // expiration time for the channel access token in seconds - 30 days
            ->header('alg','RS256')
            ->header('typ','JWT')
            ->header('kid',$private_key['kid'])
            ->sign($jwk);
        return $jws;
    }

    private function handleError($e) 
    {
        \Log::error($e);

        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();            
        $response = json_decode($responseBodyAsString,1);

        $message = __('messages.line-api-error'). ": ";
        if (isset($response['message'])) {
            $message .=  @$response['message'];
        } else {
            $message .=  @$response['error'] . ' ' .@$response['error_description'];
        }

        $out['status'] = 0;
        $out['message'] = $message;
        return $out;
    }
}