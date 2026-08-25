<?php

test('the login page returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('protected routes redirect guests to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
