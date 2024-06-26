<?php

namespace Tests;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait MakeJsonAPiRequest
{
  protected bool $formattedJsonApiDocument = true;

  public function json($method, $uri, array $data = [], array $headers = []): TestResponse
  {
    $headers['accept'] = 'application/vnd.api+json';

    if ($this->formattedJsonApiDocument) {
      $formattedData = $this->getFormattedData($uri, $data);
    }

    return parent::json($method, $uri, $formattedData ?? $data, $headers);
  }

  public function postJson($uri, array $data = [], array $headers = []): TestResponse
  {
    $headers['content-type'] = 'application/vnd.api+json';
    return parent::postJson($uri, $data, $headers);
  }

  public function patchJson($uri, array $data = [], array $headers = []): TestResponse
  {
    $headers['content-type'] = 'application/vnd.api+json';
    return parent::patchJson($uri, $data, $headers);
  }

  public function withoutFormattedData()
  {
    $this->formattedJsonApiDocument = false;
  }

  public function getFormattedData(string $uri, array $data): array
  {
    $path = parse_url($uri)['path'];
    $type = (string) Str::of($path)->after('/api/v1/')->before('/');
    $id = (string) Str::of($path)->after($type)->replace('/', '');

    return [
      'data' => [
        'attributes' => $data,
        'type' => $type,
        'id' => $id
      ]
    ];
  }
}
