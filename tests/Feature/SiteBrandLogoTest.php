<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('memakai logo dark di nav sticky dan footer (latar gelap)', function () {
    $html = get(route('news.index'))
        ->assertSuccessful()
        ->getContent();

    expect($html)
        ->toContain('images/brand/logo-dark.png')
        ->toContain('id="nav-sticky-logo"')
        ->toContain('h-12 md:h-14')
        ->toContain('md:max-w-[340px]')
        ->toContain('h-16 md:h-20 w-auto max-w-full')
        ->toContain('lg:col-span-4 min-w-0')
        ->not->toContain('brightness-0 invert');
});
