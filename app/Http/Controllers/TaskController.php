<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;


class TaskController extends Controller
{
    public function index()
    {
        try {
            $tasks = Task::all();
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($tasks, JsonResponse::HTTP_OK);
    }

    public function show($id)
    {
        try {
            $task = Task::find($id);
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($task, JsonResponse::HTTP_OK);
    }

    public function store(Request $request)
    {

        // validation
        try
        {
            $request->validate([
                'name' => 'required|min:3|max:100',
                'description' => 'required|min:10|max:5000',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // create a task
        try
        {
            $task = Task::create($request->all());
        } catch (Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $signature = preg_replace("/.+\?signature=(.+)/", "$1", URL::signedRoute('task.update', $task->id));
        return response()->json(['signature'=>$signature, 'task'=>$task], JsonResponse::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        // validation - no longer required but we still need lower and upper limits
        try
        {
            $request->validate([
                'name' => 'min:3|max:100',
                'description' => 'min:10|max:5000',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // update a task
        try
        {
            $task = Task::find($id);
            $task->update($request->all());
        } catch (Exception $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($task, JsonResponse::HTTP_OK);
    }

    public function delete($id)
    {

        // (soft) delete a task
        try
        {
            $task = Task::destroy($id);
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message'=>$e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($task, JsonResponse::HTTP_NO_CONTENT);
    }
}
