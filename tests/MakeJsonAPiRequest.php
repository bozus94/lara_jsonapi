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
      $formattedData['data']['attributes'] = $data;
      $formattedData['data']['type'] = (string) Str::of($uri)->after('/api/v1/');
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
}
