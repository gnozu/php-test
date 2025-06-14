<?php
 
namespace Tests\Unit;
 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
 
class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that soft deletion works
     */
    public function test_soft_delete(): void
    {
        // create a user
        $task = Task::factory()->create();
        $task->delete();
        
        $this->assertSoftDeleted($task);
    }
}