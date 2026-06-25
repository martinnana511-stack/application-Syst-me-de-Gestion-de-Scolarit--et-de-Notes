<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FirebaseService
{
    private string $projectId;
    private string $credentialsPath;

    public function __construct()
    {
        $credentials = json_decode(file_get_contents(base_path(env('FIREBASE_CREDENTIALS'))), true);
        $this->projectId = $credentials['project_id'];
        $this->credentialsPath = base_path(env('FIREBASE_CREDENTIALS'));
    }

    private function getAccessToken(): string
    {
        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            json_decode(file_get_contents($this->credentialsPath), true)
        );

        $token = $credentials->fetchAuthToken();
        return $token['access_token'];
    }

    public function envoyerNotification(string $fcmToken, string $titre, string $corps): bool
    {
        try {
            $accessToken = $this->getAccessToken();
            $client = new Client();

            $response = $client->post(
                "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => [
                        'message' => [
                            'token'        => $fcmToken,
                            'notification' => [
                                'title' => $titre,
                                'body'  => $corps,
                            ],
                        ],
                    ],
                ]
            );

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function envoyerNotificationGroupe(array $fcmTokens, string $titre, string $corps): void
    {
        foreach ($fcmTokens as $token) {
            $this->envoyerNotification($token, $titre, $corps);
        }
    }
}