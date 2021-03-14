# laravel-openapi-validate

OpenAPI API testing in Laravel projects.

```
<?php

namespace Tests\Api\Mobile;

use Tests\TestCase;
use Tests\Api\Mobile\OpenApiSchemaValidate;

class ApiTest extends TestCase
{
    use OpenApiSchemaValidate;
    
    public function setUp()
    {
        parent::setUp();

        $this->buildResponseValidatorFromJson(file_get_contents('open-api.json'));
    }

    public function testIndex()
    {
        $this->get( '/', []);
    }
}
```
