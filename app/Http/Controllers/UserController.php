<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;


class UserController extends Controller {
    private $userRepository;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
        $this->userRepository = new UserRepository();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request) {
        $user = $this->userRepository->find($request->route('userId'));
        return response()->json($user, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $created = $this->userRepository->create($request->only([
            'name',
            'email',
            'password'
        ]));
        return response()->json($created, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        $user = $this->userRepository->find($request->route('userId'));
        if (!$user) {
            throw new \Exception('User not found');
        }

        $this->userRepository->update($user, $request->only([
            'name',
            'email',
            'password'
        ]));

        return response()->json($user, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request) {
        $user = $this->userRepository->find($request->route('userId'));
        $deleted = $this->userRepository->forceDelete($user);
        return response()->json($deleted, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $results = $this->userRepository->searchByName($request->name);
        return response()->json($results, 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function following() {
        $results = $this->userRepository->following();
        return response()->json($results, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow(Request $request) {
        $result = $this->userRepository->follow($request->follower_id);
        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unfollow(Request $request) {
        $result = $this->userRepository->unfollow($request->user_id);
        return response()->json($result, 200);
    }
}