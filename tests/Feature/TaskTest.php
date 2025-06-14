<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Models\Task;
use Faker\Generator as Faker;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    /**
     * simple index
     */
    public function test_api_index(): void
    {
        $this->seed();
  
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(
                [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                        'deleted_at',
                        'updated_at',
                    ]
                ]
        );


    }

    /**
     * show a task
     */
    public function test_api_show(): void
    {
        $task = Task::factory()->create();
        $response = $this->get(route('task.show', '1'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $task->name]);
        $response->assertJsonFragment(['description' => $task->description]);
    }

    /**
     * create a task
     */
    public function test_api_store(): void
    {
        $task = Task::factory()->make();
        $response = $this->post(route('task.store'), ['name' => $task->name, 'description' => $task->description]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $task->name]);
    }

    /**
     * create a task but with invalid data, so it shouldn't work
     */
    public function test_api_store_with_invalid_data_should_fail(): void
    {
        $task = Task::factory()->make();

        // the name has to be >=3 chars
        $response = $this->post(route('task.store'), ['name' => 'a', 'description' => $task->description]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // the description has to be >= 10 chars
        $response = $this->post(route('task.store'), ['name' => $task->name, 'description' => 'a']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * update a task
     */
    public function test_api_update(): void
    {
        // first, create a task and check it's ok
        $task = Task::factory()->make();
        $response = $this->post(route('task.store'), ['name' => $task->name, 'description' => $task->description]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $task->name]);

        // note that task's signature and id
        $signature = $response->json()['signature'];
        $id = $response->json()['task']['id'];
        
        // make an update to the task
        $new_task = Task::factory()->make();
        $response = $this->put(route('task.update', [$id, 'signature' => $signature]), ['name' => $new_task->name]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $new_task->name]);

    }

    /**
     * delete a task
     */
    public function test_api_delete(): void
    {
        // first, create a task and check it's ok
        $task = Task::factory()->make();
        $response = $this->post(route('task.store'), ['name' => $task->name, 'description' => $task->description]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => $task->name]);

        // note that task's signature and id
        $signature = $response->json()['signature'];
        $id = $response->json()['task']['id'];
        
        // delete the task
        $response = $this->delete(route('task.delete', [$id, 'signature' => $signature]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);

    }

    
}
