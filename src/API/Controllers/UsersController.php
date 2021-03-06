<?php

namespace SlimKit\PlusID\API\Controllers;

use Illuminate\Http\Request;
use Zhiyi\Plus\Models\User as UserModel;
use SlimKit\PlusID\Models\Client as ClientModel;

class UsersController
{
    public function check(Request $request, ClientModel $client)
    {
        $map = $request->only(['phone', 'name', 'email']);
        $sign = $request->input('sign');
        $time = $request->input('time');

        // 过期 五分钟 5 * 60
        if ((time() - $time) > 300) {
            $message = ['status' => 'fail', 'code' => 10000];

            return response()->json($message, 422);
        }

        // 签名失败。
        $action = [
            'app' => $client->id,
            'action' => 'user/check',
            'time' => $time,
        ];
        if ($client->sign($action) !== $sign) {
            $message = ['status' => 'fail', 'code' => 10001];

            return response()->json($message, 403);
        }

        foreach ($map as $key => &$value) {
            if (! $value) {
                continue;
            }

            $value = (bool) UserModel::where($key, $value)->first();
        }

        return response()->json(['status' => 'success', 'map' => $map], 201);
    }

    public function show(Request $request, ClientModel $client)
    {
        $sign = $request->input('sign');
        $time = $request->input('time');
        $user = $request->input('user');

        // 过期 五分钟 5 * 60
        if ((time() - $time) > 300) {
            $message = ['status' => 'fail', 'code' => 10000];

            return response()->json($message, 422);
        }

        // 签名失败。
        $action = [
            'app' => $client->id,
            'action' => 'user/show',
            'time' => $time,
        ];
        if ($client->sign($action) !== $sign) {
            $message = ['status' => 'fail', 'code' => 10001];

            return response()->json($message, 403);
        }

        $user = UserModel::find($user);
        if (! $user) {
            return response()->json(['status' => 'fial', 'code' => 10003], 404);
        }

        return $user;
    }

    public function create(Request $request, ClientModel $client)
    {
        // todo.
    }
}
