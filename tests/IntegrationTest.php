<?php

namespace Jaulz\Lingua\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Jaulz\Lingua\Facades\Lingua;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
    DB::statement('CREATE EXTENSION IF NOT EXISTS hstore');

    $migration = include __DIR__ . '/../database/migrations/create_lingua_extension.php.stub';
    $migration->up();
});

test('determines correct language', function () {
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->text('body');
    });

    Schema::table('posts', function (Blueprint $table) {
        $table->lingua(['title', 'body']);
    });

    collect([
        'this is a test' => "en",
        'crapcrapcrapcrap' => null,
        'dies ist ein test' => 'de',
        'c\'est un test' => 'fr',
        'questa è una prova' => 'it',
        'dette er en test' => 'da',
        'דאָס איז אַ פּראָבע' => null,
        '这是一个测试' => null,
    ])->each(function ($value, $key) {
        // Create post
        $post = DB::table('posts')->insertReturning([
            'title' => $key,
            'body' => $key,
        ])->first();

        expect($post->language_code)->toBe($value);
    });
});
