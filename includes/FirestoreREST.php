<?php
// includes/FirestoreREST.php

require_once __DIR__ . '/../vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class FirestoreREST {
    private $projectId;
    private $httpClient;
    private $baseUrl;

    public function __construct($jsonKeyPath) {
        if (!file_exists($jsonKeyPath)) {
            throw new Exception("Firebase credentials not found at $jsonKeyPath");
        }

        $keyFile = json_decode(file_get_contents($jsonKeyPath), true);
        $this->projectId = $keyFile['project_id'];
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/";

        // Setup Google Auth Middleware for Guzzle
        $scopes = ['https://www.googleapis.com/auth/datastore'];
        $creds = new ServiceAccountCredentials($scopes, $keyFile);
        $middleware = new AuthTokenMiddleware($creds);

        $stack = HandlerStack::create();
        $stack->push($middleware);

        $this->httpClient = new Client([
            'handler' => $stack,
            'auth' => 'google_auth'
        ]);
    }

    private function encodeFields($data) {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_array($value)) {
                $fields[$key] = ['stringValue' => json_encode($value)]; // Simple fallback
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }

    private function decodeFields($fields) {
        $data = [];
        if (!$fields) return $data;
        foreach ($fields as $key => $valObj) {
            if (isset($valObj['stringValue'])) {
                $data[$key] = $valObj['stringValue'];
            } elseif (isset($valObj['integerValue'])) {
                $data[$key] = (int)$valObj['integerValue'];
            } elseif (isset($valObj['doubleValue'])) {
                $data[$key] = (float)$valObj['doubleValue'];
            } elseif (isset($valObj['booleanValue'])) {
                $data[$key] = (bool)$valObj['booleanValue'];
            } elseif (isset($valObj['nullValue'])) {
                $data[$key] = null;
            } else {
                $data[$key] = json_encode($valObj);
            }
        }
        return $data;
    }

    public function getDocuments($collectionPath) {
        try {
            $response = $this->httpClient->get($this->baseUrl . $collectionPath);
            $body = json_decode($response->getBody(), true);
            $docs = [];
            if (isset($body['documents'])) {
                foreach ($body['documents'] as $doc) {
                    $parts = explode('/', $doc['name']);
                    $id = end($parts);
                    $data = $this->decodeFields($doc['fields'] ?? []);
                    $data['id'] = $id;
                    $docs[] = $data;
                }
            }
            return $docs;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getDocument($collectionPath, $documentId) {
        try {
            $response = $this->httpClient->get($this->baseUrl . $collectionPath . '/' . $documentId);
            $body = json_decode($response->getBody(), true);
            $data = $this->decodeFields($body['fields'] ?? []);
            $data['id'] = $documentId;
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function newDocument($collectionPath, $data) {
        try {
            $payload = ['fields' => $this->encodeFields($data)];
            $response = $this->httpClient->post($this->baseUrl . $collectionPath, [
                'json' => $payload
            ]);
            $body = json_decode($response->getBody(), true);
            $parts = explode('/', $body['name']);
            return end($parts);
        } catch (\Exception $e) {
            throw new Exception("Error creating document: " . $e->getMessage());
        }
    }

    public function setDocument($collectionPath, $documentId, $data) {
        try {
            $payload = ['fields' => $this->encodeFields($data)];
            $url = $this->baseUrl . $collectionPath . '/' . $documentId;
            
            // Generate updateMask for merge behavior
            $updateMask = [];
            foreach ($data as $k => $v) {
                $updateMask[] = "updateMask.fieldPaths=" . urlencode($k);
            }
            if (!empty($updateMask)) {
                $url .= '?' . implode('&', $updateMask);
            }
            
            $this->httpClient->patch($url, [
                'json' => $payload
            ]);
            return true;
        } catch (\Exception $e) {
            throw new Exception("Error setting document: " . $e->getMessage());
        }
    }

    public function deleteDocument($collectionPath, $documentId) {
        try {
            $this->httpClient->delete($this->baseUrl . $collectionPath . '/' . $documentId);
            return true;
        } catch (\Exception $e) {
            throw new Exception("Error deleting document: " . $e->getMessage());
        }
    }
}
?>
