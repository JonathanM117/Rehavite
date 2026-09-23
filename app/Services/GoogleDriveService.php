<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * GoogleDriveService
 *
 * Handles file uploads to Google Drive using a Service Account.
 *
 * How to configure:
 * 1. Go to Google Cloud Console > APIs & Services > Credentials.
 * 2. Create a Service Account and download the JSON key file.
 * 3. Save the JSON key path in .env as GOOGLE_DRIVE_KEY_FILE (e.g., /path/to/service-account.json)
 *    OR paste the entire JSON content into GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON.
 * 4. In Google Drive, share the target folder with the service account email (found in the JSON),
 *    granting it "Editor" permissions.
 * 5. Copy the folder ID from the Drive URL and set it as GOOGLE_DRIVE_FOLDER_ID in .env.
 */
class GoogleDriveService
{
    private string $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    private string $uploadEndpoint = 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart';
    private string $driveApiEndpoint = 'https://www.googleapis.com/drive/v3/files';

    /**
     * Check if Google Drive credentials are configured.
     */
    public function isConfigured(): bool
    {
        return (bool) (config('services.google_drive.folder_id') &&
                      (config('services.google_drive.key_file') || config('services.google_drive.service_account_json')));
    }

    /**
     * Upload a file to Google Drive and return its web view URL.
     *
     * @param  string  $filePath   Absolute path to the local file
     * @param  string  $fileName   Name the file will have in Drive
     * @param  string  $mimeType   MIME type of the file (e.g., 'application/pdf')
     * @return string|null         The web view URL if successful, null otherwise
     */
    public function upload(string $filePath, string $fileName, string $mimeType = 'application/octet-stream'): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('GoogleDriveService: Service account credentials not configured.');
            return null;
        }

        try {
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('GoogleDriveService: Could not obtain access token.');
                return null;
            }

            $folderId = config('services.google_drive.folder_id');

            // Build multipart request
            $fileContent = file_get_contents($filePath);
            $boundary = '----FormBoundary' . uniqid();

            $metadata = json_encode([
                'name'    => $fileName,
                'parents' => [$folderId],
            ]);

            $body = "--{$boundary}\r\n"
                . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
                . $metadata . "\r\n"
                . "--{$boundary}\r\n"
                . "Content-Type: {$mimeType}\r\n\r\n"
                . $fileContent . "\r\n"
                . "--{$boundary}--";

            $ch = curl_init($this->uploadEndpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: multipart/related; boundary={$boundary}",
                    'Content-Length: ' . strlen($body),
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('GoogleDriveService: Upload failed.', ['response' => $response, 'code' => $httpCode]);
                return null;
            }

            $data = json_decode($response, true);
            $fileId = $data['id'] ?? null;

            if (!$fileId) {
                Log::error('GoogleDriveService: No file ID in response.', ['response' => $response]);
                return null;
            }

            // Make the file accessible (readable link)
            $this->makePubliclyReadable($fileId, $accessToken);

            return "https://drive.google.com/file/d/{$fileId}/view";

        } catch (\Throwable $e) {
            Log::error('GoogleDriveService: Exception during upload.', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtain an OAuth2 access token using a Service Account JWT.
     */
    private function getAccessToken(): ?string
    {
        // Load service account credentials
        $credentials = $this->loadCredentials();
        if (!$credentials) return null;

        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claimSet = base64_encode(json_encode([
            'iss'   => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file',
            'aud'   => $this->tokenEndpoint,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claimSet}";
        $privateKey = $credentials['private_key'];

        // Sign the JWT with the private key
        $signature = '';
        openssl_sign($signingInput, $signature, $privateKey, 'SHA256');
        $signature = base64_encode($signature);

        // URL-safe base64
        $jwt = str_replace(['+', '/', '='], ['-', '_', ''], "{$signingInput}.{$signature}");

        // Exchange JWT for access token
        $ch = curl_init($this->tokenEndpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    /**
     * Grant public read access to a file in Drive (anyone with the link can view).
     */
    private function makePubliclyReadable(string $fileId, string $accessToken): void
    {
        $ch = curl_init("{$this->driveApiEndpoint}/{$fileId}/permissions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['role' => 'reader', 'type' => 'anyone']),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
            ],
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Load service account credentials from config/env.
     */
    private function loadCredentials(): ?array
    {
        $jsonContent = config('services.google_drive.service_account_json');

        if (!$jsonContent) {
            $keyFile = config('services.google_drive.key_file');
            if ($keyFile && file_exists($keyFile)) {
                $jsonContent = file_get_contents($keyFile);
            }
        }

        if (!$jsonContent) return null;

        $credentials = json_decode($jsonContent, true);
        if (!isset($credentials['client_email'], $credentials['private_key'])) {
            Log::error('GoogleDriveService: Invalid service account JSON structure.');
            return null;
        }

        return $credentials;
    }
}
