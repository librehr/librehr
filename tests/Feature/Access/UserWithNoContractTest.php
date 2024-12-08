<?php

beforeEach(function () {
    login();
});

test('cant access to  contract related', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertForbidden();
})->with('user_contract_resources');

test('can access to profile related', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertOk();
})->with('profile_resources');

test('cant access resource manager', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertForbidden();
})->with('manager_resources');

test('cant access business dataset', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertForbidden();
})->with('business_resources');

test('can access business', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertOk();
})->with([
    \App\Filament\App\Resources\TeamResource::class,
]);

test('cant access administration', function (string $url) {
    $this->get((new $url)->getNavigationUrl())->assertForbidden();
})->with('administration_resources');
