<?php

namespace security;

use PHPUnit\Framework\TestCase;

// doesn't actually work or give any value, and haven't been able to test properly
class GjestTest extends TestCase
{
    private $baseUrl = "http://158.39.188.201/Steg2/";

    public function testBruteforceRetrievePinsFails() // similar test could be made for check.php and emne.php
    {
        $curl = curl_init();

        $url = $this->baseUrl . "gjest_bruker_autentisering.php";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);

        //should loop through all possible pins and subject_ids
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'pin' => '1234',
            'subject_id' => '5678',
        ]);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        // sjekker at kombinasjonen ikke er gyldig
        $this->assertStringNotContainsString("Location: emne.php", $response);
    }
}