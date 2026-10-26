<?php

namespace Tests\Unit;

use App\Http\Requests\ProfileRequest;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProfileRequestTest extends TestCase
{

    private $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new class extends ProfileRequest {
            public function sanitize($value)
            {
                return $this->sanitizeString($value);
            }
        };
    }


    #[Test]
    public function quotes_are_html_encoded()
    {
        $this->assertSame('O&#39;Brien &#34;Jr&#34;', $this->request->sanitize('O\'Brien "Jr"'));
    }

    #[Test]
    public function tags_are_stripped()
    {
        $this->assertSame('V8R 5N5', $this->request->sanitize('<b>V8R</b> 5N5'));
    }

    #[Test]
    public function a_less_than_sign_followed_by_a_space_starts_a_tag()
    {
        $this->assertSame('V8R ', $this->request->sanitize('V8R < 5N5'));
    }

    #[Test]
    public function null_becomes_an_empty_string()
    {
        $this->assertSame('', $this->request->sanitize(null));
    }

    #[Test]
    public function an_array_is_rejected()
    {
        $this->assertFalse($this->request->sanitize(['V8R 5N5']));
    }

}
