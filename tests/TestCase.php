<?php

namespace Tests;

use Tests\MakeJsonAPiRequest;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MakeJsonAPiRequest;
}
