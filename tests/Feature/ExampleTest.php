<?php

it('returns a successful response', function () {
    $response = $this->get('/');
    
    $response->assertStatus(fn ($status) => 
        in_array($status, [200, 302])
    );
});
