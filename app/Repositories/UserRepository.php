<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserFollowing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository {
    private $table = 'users';
    private $tableFollowing = 'user_following';
    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
           $created = User::query()->create([
               'name' => data_get($attributes, 'name', 'Default'),
               'email' => data_get($attributes, 'email', ''),
               'password' => Hash::make(data_get($attributes, 'password', ''))
           ]);

           return $created;
        });
    }

    /**
     * @param $model
     * @param array $attributes
     * @return mixed
     */
    public function update($model, array $attributes)
    {
        return DB::transaction(function () use ($model, $attributes) {
            $updated = $model->update([
                'name' => data_get($attributes, 'name', 'Default'),
                'email' => data_get($attributes, 'email', ''),
                'password' => Hash::make(data_get($attributes, 'password', ''))
            ]);

            if (!$updated) {
                throw new \Exception('Update failed');
            }

            return $updated;
        });
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id) {
        $result = User::find($id);

        return $result;
    }

    /**
     * @param $model
     * @return mixed
     */
    public function forceDelete($model)
    {
        return DB::transaction(function () use ($model) {
            $deleted = $model->forceDelete();
            if (!$deleted) {
                throw new \Exception('Delete faield');
            }

            return $deleted;
        });

    }

    /**
     * @param $name
     * @return \Illuminate\Support\Collection
     */
    public function searchByName($name, $isLike = true) {
        $operator = ($isLike)? 'LIKE' : '=';
        $value = ($isLike)? '%' . $name . '%' : $name;
        $users = DB::table($this->table)
            ->select('name', 'email')
            ->where('name', $operator, $value)->get();
        return $users;
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function follow($userId) {
        return DB::transaction(function () use ($userId) {
            $user = $this->find($userId);
            if ($user && !$this->checkFollowStatusById(auth()->user()->id, $userId)) {
                $created = UserFollowing::query()->create([
                    'user_id' => auth()->user()->id,
                    'following_id' => $userId,
                ]);
                return $created;
            } else {
                throw new \Exception('Failed');
            }
        });
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function unFollow($userId) {
        return DB::transaction(function () use ($userId) {
            $userFollowing = DB::table($this->tableFollowing)
                ->select('id')
                ->where('user_id', '=', auth()->user()->id)
                ->where('following_id', '=', $userId);
            if (!$userFollowing->exists()) {
                throw new \Exception('Failed');

            }

            return $userFollowing->delete();
        });
    }

    public function following() {
        $results = DB::table('users')->select('name', 'email')->whereIn('id', function($query){
            $query->select('following_id')
                ->from($this->tableFollowing)
                ->where('user_id', '=', auth()->user()->id);
        })->get();
        return $results;
    }

    /**
     * @param $userId
     * @param $followingId
     * @return bool
     */
    public function checkFollowStatusById ($userId, $followingId) {
        return DB::table($this->tableFollowing)
            ->select('id')
            ->where('user_id', '=', $userId)
            ->where('following_id', '=', $followingId)
            ->exists();
    }
}