<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FirebaseService
{
  protected $projectId;
  protected $accessToken;
  protected $baseUrl;

  public function __construct()
  {
    $this->projectId = env('FIREBASE_PROJECT_ID', 'absensi-pegawai-app');
    $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    $this->accessToken = $this->getAccessToken();
  }

  /**
   * Get access token from service account
   */
  protected function getAccessToken()
  {
    try {
      // Gunakan base_path karena di .env sudah menyertakan folder 'storage/'
      $serviceAccountPath = base_path(env('FIREBASE_CREDENTIALS'));

      if (!file_exists($serviceAccountPath)) {
        // Fallback: Coba cek jika path di .env tidak menyertakan 'storage/'
        $serviceAccountPath = storage_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($serviceAccountPath)) {
          throw new \Exception("Service account file not found at: " . base_path(env('FIREBASE_CREDENTIALS')));
        }
      }

      $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

      // Create JWT
      $now = time();
      $exp = $now + 3600;

      $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
      $claim = json_encode([
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/firebase',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $exp,
        'iat' => $now
      ]);

      $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
      $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));

      $signature = '';
      openssl_sign(
        $base64UrlHeader . "." . $base64UrlClaim,
        $signature,
        $serviceAccount['private_key'],
        'SHA256'
      );

      $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
      $jwt = $base64UrlHeader . "." . $base64UrlClaim . "." . $base64UrlSignature;

      // Exchange JWT for access token
      $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
      ]);

      if ($response->successful()) {
        return $response->json()['access_token'];
      }

      throw new \Exception("Failed to get access token: " . $response->body());
    } catch (\Exception $e) {
      throw new \Exception("Error getting access token: " . $e->getMessage());
    }
  }

  /**
   * Get all documents from a collection
   * Using runQuery API for better limit/pagination support
   */
  public function getCollection($collection, $limit = 1000)
  {
    try {
      // Use runQuery API instead of simple GET for better limit support
      $query = [
        'structuredQuery' => [
          'from' => [['collectionId' => $collection]],
          'limit' => $limit
        ]
      ];

      $response = Http::withToken($this->accessToken)
        ->post("{$this->baseUrl}:runQuery", $query);

      if (!$response->successful()) {
        throw new \Exception("Failed to fetch collection: " . $response->body());
      }

      $results = $response->json();
      $documents = [];

      foreach ($results as $result) {
        if (isset($result['document'])) {
          $doc = $result['document'];
          $documents[] = [
            'id' => $this->getDocumentId($doc['name']),
            'data' => $this->parseFirestoreDocument($doc['fields'] ?? [])
          ];
        }
      }

      return $documents;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Get a specific document
   */
  public function getDocument($collection, $documentId)
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->get("{$this->baseUrl}/{$collection}/{$documentId}");

      if (!$response->successful()) {
        if ($response->status() === 404) {
          return null;
        }
        throw new \Exception("Failed to fetch document: " . $response->body());
      }

      $doc = $response->json();

      return [
        'id' => $this->getDocumentId($doc['name']),
        'data' => $this->parseFirestoreDocument($doc['fields'] ?? [])
      ];
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Query documents with where clause
   */
  public function queryCollection($collection, $field, $operator, $value, $limit = null)
  {
    try {
      $query = [
        'structuredQuery' => [
          'from' => [['collectionId' => $collection]],
          'where' => [
            'fieldFilter' => [
              'field' => ['fieldPath' => $field],
              'op' => $this->getOperator($operator),
              'value' => $this->convertToFirestoreValue($value)
            ]
          ]
        ]
      ];

      if ($limit) {
        $query['structuredQuery']['limit'] = $limit;
      }

      $response = Http::withToken($this->accessToken)
        ->post("{$this->baseUrl}:runQuery", $query);

      if (!$response->successful()) {
        throw new \Exception("Failed to query collection: " . $response->body());
      }

      $results = $response->json();
      $documents = [];

      foreach ($results as $result) {
        if (isset($result['document'])) {
          $doc = $result['document'];
          $documents[] = [
            'id' => $this->getDocumentId($doc['name']),
            'data' => $this->parseFirestoreDocument($doc['fields'] ?? [])
          ];
        }
      }

      return $documents;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Create a new document
   */
  public function createDocument($collection, $data, $documentId = null)
  {
    try {
      $url = "{$this->baseUrl}/{$collection}";

      if ($documentId) {
        $url .= "?documentId={$documentId}";
      }

      $response = Http::withToken($this->accessToken)
        ->post($url, [
          'fields' => $this->convertToFirestoreFormat($data)
        ]);

      if (!$response->successful()) {
        throw new \Exception("Failed to create document: " . $response->body());
      }

      $doc = $response->json();

      return [
        'id' => $this->getDocumentId($doc['name']),
        'data' => $this->parseFirestoreDocument($doc['fields'] ?? [])
      ];
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Update a document
   */
  public function updateDocument($collection, $documentId, $data)
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->patch("{$this->baseUrl}/{$collection}/{$documentId}", [
          'fields' => $this->convertToFirestoreFormat($data)
        ]);

      if (!$response->successful()) {
        throw new \Exception("Failed to update document: " . $response->body());
      }

      $doc = $response->json();

      return [
        'id' => $this->getDocumentId($doc['name']),
        'data' => $this->parseFirestoreDocument($doc['fields'] ?? [])
      ];
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Delete a document
   */
  public function deleteDocument($collection, $documentId)
  {
    try {
      $response = Http::withToken($this->accessToken)
        ->delete("{$this->baseUrl}/{$collection}/{$documentId}");

      return $response->successful();
    } catch (\Exception $e) {
      throw $e;
    }
  }

  // Helper methods

  protected function getDocumentId($name)
  {
    $parts = explode('/', $name);
    return end($parts);
  }

  protected function getOperator($operator)
  {
    $operators = [
      '=' => 'EQUAL',
      '==' => 'EQUAL',
      '<' => 'LESS_THAN',
      '<=' => 'LESS_THAN_OR_EQUAL',
      '>' => 'GREATER_THAN',
      '>=' => 'GREATER_THAN_OR_EQUAL',
      '!=' => 'NOT_EQUAL',
      'array-contains' => 'ARRAY_CONTAINS',
      'in' => 'IN',
      'array-contains-any' => 'ARRAY_CONTAINS_ANY',
    ];

    return $operators[$operator] ?? 'EQUAL';
  }

  protected function convertToFirestoreValue($value)
  {
    // Tambahkan blok ini di paling atas
    if ($value instanceof \DateTimeInterface) {
      return ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
    } elseif (is_string($value)) {
      return ['stringValue' => $value];
    } elseif (is_int($value)) {
      return ['integerValue' => (string) $value];
    } elseif (is_float($value)) {
      return ['doubleValue' => $value];
    } elseif (is_bool($value)) {
      return ['booleanValue' => $value];
    } elseif (is_null($value)) {
      return ['nullValue' => null];
    } elseif (is_array($value)) {
      return ['arrayValue' => ['values' => array_map([$this, 'convertToFirestoreValue'], $value)]];
    }

    return ['stringValue' => (string) $value];
  }

  protected function convertToFirestoreFormat($data)
  {
    $formatted = [];

    foreach ($data as $key => $value) {
      // Tambahkan blok ini di paling atas loop
      if ($value instanceof \DateTimeInterface) {
        $formatted[$key] = ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
      } elseif (is_string($value)) {
        $formatted[$key] = ['stringValue' => $value];
      } elseif (is_int($value)) {
        $formatted[$key] = ['integerValue' => (string) $value];
      } elseif (is_float($value)) {
        $formatted[$key] = ['doubleValue' => $value];
      } elseif (is_bool($value)) {
        $formatted[$key] = ['booleanValue' => $value];
      } elseif (is_null($value)) {
        $formatted[$key] = ['nullValue' => null];
      } elseif (is_array($value)) {
        $formatted[$key] = ['mapValue' => ['fields' => $this->convertToFirestoreFormat($value)]];
      }
    }

    return $formatted;
  }

  protected function parseFirestoreDocument($fields)
  {
    $data = [];

    foreach ($fields as $key => $value) {
      if (isset($value['stringValue'])) {
        $data[$key] = $value['stringValue'];
      } elseif (isset($value['integerValue'])) {
        $data[$key] = (int) $value['integerValue'];
      } elseif (isset($value['doubleValue'])) {
        $data[$key] = $value['doubleValue'];
      } elseif (isset($value['booleanValue'])) {
        $data[$key] = $value['booleanValue'];
      } elseif (isset($value['timestampValue'])) {
        $data[$key] = $value['timestampValue'];
      } elseif (isset($value['nullValue'])) {
        $data[$key] = null;
      } elseif (isset($value['arrayValue'])) {
        $data[$key] = array_map(function ($item) {
          return $this->parseFirestoreValue($item);
        }, $value['arrayValue']['values'] ?? []);
      } elseif (isset($value['mapValue'])) {
        $data[$key] = $this->parseFirestoreDocument($value['mapValue']['fields'] ?? []);
      }
    }

    return $data;
  }

  protected function parseFirestoreValue($value)
  {
    if (isset($value['stringValue']))
      return $value['stringValue'];
    if (isset($value['integerValue']))
      return (int) $value['integerValue'];
    if (isset($value['doubleValue']))
      return $value['doubleValue'];
    if (isset($value['booleanValue']))
      return $value['booleanValue'];
    if (isset($value['timestampValue']))
      return $value['timestampValue'];
    if (isset($value['nullValue']))
      return null;
    if (isset($value['mapValue']))
      return $this->parseFirestoreDocument($value['mapValue']['fields'] ?? []);

    return null;
  }
}
