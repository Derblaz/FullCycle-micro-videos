<?php

namespace Tests\Unitz\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\Traits\Uuid;

class CategoryTest extends TestCase
{
    public function testIfUseTraits()
    {
        $trais = [
            SoftDeletes::class,
            Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($trais, $categoryTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        foreach ($dates as $date) {
            $this->assertContains($date, $category->getDates());
        }
        $this->assertCount(count($dates), $category->getDates());
    }

    public function testCastAttribute()
    {
        $cast = [
            'id' => 'string',
            'is_active' => 'boolean'
        ];
        $category = new Category();
        $this->assertEquals($cast, $category->getCasts());
    }
}
