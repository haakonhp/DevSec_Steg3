<?php

namespace security;

use PHPUnit\Framework\TestCase;

// probably doesn't work, haven't been able to test properly
class SignupTest extends TestCase
{
    private $baseUrl = "http://158.39.188.201/Steg2/";

    public function testUploadPhpFails()
    {
        $curl = curl_init();
        $file_path = __DIR__ . '/tests/security/testFiles/test.php';

        // set up the POST data with a PHP file as the image
        $post_data = [
            'signup' => 'signup',
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'testpassword',
            'password_confirmation' => 'testpassword',
            'subjects' => '1234',
            'bilde' => curl_file_create($file_path, 'image/png', 'test.php')
        ];

        $url = $this->baseUrl . "ansatt_signup.php";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        // check that the response doesn't contain the PHP code
        $this->assertStringNotContainsString('<?php', $response);
        // check that the response contains an error message
        $this->assertStringContainsString('Feil ved opplasting av bilde', $response);
    }
}