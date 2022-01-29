<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'data' => $this->userService->fetchAll($request->all()),
        ]);
    }


    public function store(UserRequest $request)
    {
        $data = $request->only(array_keys($request->rules()));
        $data['password'] = bcrypt($data['password']);

        return response()->json([
            'data' => $this->userService->save($data),
            'message' => 'O usuário foi cadastrado com sucesso.'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'data' => $this->userService->findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->only(array_keys($request->rules()));
        $data['id'] = $id;

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $this->userService->save($data);
        return response()->json([
            'data' => [],
            'message' => 'O usuário foi editado com sucesso.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->userService->delete($id);
        return response()->json([
            'data' => [],
            'message' => 'O usuário foi removido com sucesso.'
        ], 200);
    }
}
