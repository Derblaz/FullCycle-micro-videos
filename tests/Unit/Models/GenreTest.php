<?php

namespace Tests\Unitz\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Genre;
use App\Models\Traits\Uuid;

class GenreTest extends TestCase
{
    public function testIfUseTraits()
    {
        $trais = [
            SoftDeletes::class,
            Uuid::class
        ];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($trais, $genreTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];
        $genre = new Genre();
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genre = new Genre();
        foreach ($dates as $date) {
            $this->assertContains($date, $genre->getDates());
        }
        $this->assertCount(count($dates), $genre->getDates());
    }

    public function testCastAttribute()
    {
        $cast = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];
        $genre = new Genre();
        $this->assertEquals($cast, $genre->getCasts());
    }
}
