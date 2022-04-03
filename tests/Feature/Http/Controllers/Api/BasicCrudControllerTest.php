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
use Tests\Stubs\Models\CategoryStub;

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
        $resource = $this->controller->index();
        $serialized = $resource->response()->getData(true);
        $this->assertEquals(
            [$category->toArray()],
            $serialized['data']
        );
        $this->assertArrayHasKey('meta', $serialized);
        $this->assertArrayHasKey('links', $serialized);
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

        $resource = $this->controller->store($request);
        $serialized = $resource->response()->getData(true);
        $this->assertEquals( CategoryStub::first()->toArray(), $serialized['data']);
   }

   public function testIfFindOrFailFetchModel()
   {
       /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'teste_description']);

        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $resource = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $resource);
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
        $resource = $this->controller->show($category->id);
        $serialized = $resource->response()->getData(true);
        $this->assertEquals($category->toArray(), $serialized['data']);
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

        $resource = $this->controller->update($request, $category->id);
        $serialized = $resource->response()->getData(true);
        $category->refresh();
        $this->assertEquals( $category->toArray(), $serialized['data']);
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
