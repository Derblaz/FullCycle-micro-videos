<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Genre;

class GenreTest extends TestCase
{
   use DatabaseMigrations;

   public function testList()
   {
       factory(Genre::class)->create();
       $genres = Genre::all();
       $this->assertCount(1, $genres);
       $genreKey = array_keys($genres->first()->getAttributes());
       $this->assertEqualsCanonicalizing(
           [
               'id',
               'name',
               'is_active',
               'created_at',
               'updated_at',
               'deleted_at'
           ],
           $genreKey
        );
   }

   public function testeCreate()
   {
        $genre = Genre::create([
            'name' => 'test1'
        ]);
        $genre->refresh();

        $uuidPattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-[4][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        $this->assertRegExp($uuidPattern, $genre->id);
        $this->assertEquals('test1',$genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);

   }

   public function testUpdate()
   {
       /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_update',
            'is_active' => true
        ];

        $genre->update($data);

        foreach($data as $key => $value){
            $this->assertEquals($value, $genre->{$key});
        }
   }

   public function testDelete()
   {
       /** @var Genre $genre */
        factory(Genre::class, 5)->create();

        $genres = Genre::all();
        $genre = $genres->first();
        $id = $genre->id;
        $genre->delete();        

        $this->assertCount(4, Genre::all() );
        $this->assertCount(1, Genre::onlyTrashed()->get() );

        $this->assertFalse(Genre::all()->contains($id));
        $genreDeleted = Genre::onlyTrashed()->get()->first();
        $this->assertEquals($id, $genreDeleted->id);
   }
}
