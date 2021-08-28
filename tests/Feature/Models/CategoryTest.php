<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Category;

class CategoryTest extends TestCase
{
   use DatabaseMigrations;

   public function testList()
   {
       factory(Category::class, 1)->create();
       $categories = Category::all();
       $this->assertCount(1, $categories);
       $categoryKey = array_keys($categories->first()->getAttributes());
       $this->assertEqualsCanonicalizing(
           [
               'id',
               'name',
               'description',
               'is_active',
               'created_at',
               'updated_at',
               'deleted_at'
           ],
           $categoryKey
        );
   }

   public function testeCreate()
   {
        $category = Category::create([
            'name' => 'test1'
        ]);
        $category->refresh();

        $uuidPattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[4][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        $this->assertRegExp($uuidPattern, $category->id);
        $this->assertEquals('test1',$category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);

        $this->assertEquals('test_description',$category->description);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);

   }

   public function testUpdate()
   {
       /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'test_decription',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_update',
            'description' => 'test_description_update',
            'is_active' => true
        ];

        $category->update($data);

        foreach($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }
   }

   public function testDelete()
   {
       /** @var Category $category */
        factory(Category::class, 5)->create();

        $categories = Category::all();
        $category = $categories->first();
        $id = $category->id;
        $category->delete();        

        $this->assertCount(4, Category::all() );
        $this->assertCount(1, Category::onlyTrashed()->get() );

        $this->assertFalse(Category::all()->contains($id));
        $categoryDeleted = Category::onlyTrashed()->get()->first();
        $this->assertEquals($id, $categoryDeleted->id);
   }
}
