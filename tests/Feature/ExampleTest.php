<?php

it('returns a successful response', function () {
    $response = $this->get('/');
    $this->assertTrue(
        in_array($response->getStatusCode(), [200, 302]),
        'Respons seharusnya 200 atau 302, tetapi mendapatkan ' . $response->getStatusCode()
    );
});
