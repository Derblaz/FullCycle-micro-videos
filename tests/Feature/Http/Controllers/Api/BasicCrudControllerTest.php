<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery;
use ReflectionClass;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\TestCase;
use Tests\Stubs\Model\CategoryStub;

class BasicCrudControllerTest extends TestCase
{
   protected function setUp(): void
   {
       parent::setUp();
       CategoryStub::dropTable();
       CategoryStub::createTable();
       $this->controller = new CategoryControllerStub();
   }

   protected function tearDown(): void 
   {
       CategoryStub::dropTable();
       parent::tearDown();
   }

   public function testIndex()
   {
       /** @var CategoryStub $category */
       $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);       
       $result = $this->controller->index()->toArray();
       $this->assertEquals([$category->toArray()], $result);
   }

   public function testInvalidateDataInStore()
   {
        $this->expectException(ValidationException::class);

        /** @var Request $request */
        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name'=> '']);
        $this->controller->store($request);
   }

   public function testStore()
   {
        /** @var Request $request */
        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'teste_description']);

        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
        );
   }

   public function testIfFindOrFailFetchModel()
   {
       /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);

        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
   }

   public function testIfFindOrFailThrowExceptionWhenIdInvalid()
   {

        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
        $this->assertInstanceOf(CategoryStub::class, $result);
   }

   public function testShow()
   {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals($result->toArray(), CategoryStub::find(1)->toArray());
   }

   public function testUpdate()
   {
       /** @var CategoryStub $category */
       $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);

       /** @var Request $request */
       $request = Mockery::mock(Request::class);
       $request
           ->shouldReceive('all')
           ->once()
           ->andReturn(['name' => 'test_changed', 'description' => 'teste_description_changed']);

       $result = $this->controller->update($request, $category->id);
       $this->assertEquals(
           $result->toArray(),
           CategoryStub::find(1)->toArray()
       );
   }

   public function testDestroy()
   {
       /** @var CategoryStub $category */
       $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);

       $response = $this->controller->destroy($category->id);
       $this
            ->createTestResponse($response)
            ->assertStatus(204);
        $this->assertCount(0, CategoryStub::all());
   }
}
