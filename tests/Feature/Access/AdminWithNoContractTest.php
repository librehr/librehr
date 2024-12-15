<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    login('admin');
});

test('cant access to contract related', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertForbidden();
})->with('user_contract_resources');

test('can access profile related', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertOk();
})->with('profile_resources');

test('can access resource manager', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertOk();
})->with('manager_resources');

test('it can access business dataset', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertOk();
})->with('business_resources');

test('can access business', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertOk();
})->with([
    \App\Filament\App\Resources\TeamResource::class,
]);

test('can access administration', function (string $url) {
    $this->get((new $url())->getNavigationUrl())->assertOk();
})->with('administration_resources');
