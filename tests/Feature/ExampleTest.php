<?php

it('loads the login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});
