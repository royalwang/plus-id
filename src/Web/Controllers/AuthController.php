<?php

namespace SlimKit\PlusID\Web\Controllers;

use Illuminate\Http\Request;
use SlimKit\PlusID\Models\Client as ClientModel;
use SlimKit\PlusID\Support\URL;

class AuthController
{
    public function resolve(Request $request, ClientModel $client)
    {
        $sign = $request->query('sign');
        $redirect = $request->query('redirect');
        $action = ['app' => $app, 'action' => 'auth/resolve'];
        $url = new URL($redirect);

        // 签名失败。
        if ($client->sign($action) !== $sign) {
            $url->addQuery('status', 'fail');
            $url->addQuery('code', '10001');

            return redirect((string) $url, 302);
        }

        // 用户未登陆
        $user = $request->user('web');
        if ($user === null) {
            $url->addQuery('status', 'fail');
            $url->addQuery('code', '10002');

            return redirect((string) $url, 302);
        }

        $action = ['app' => $app, 'action' => 'auth/resolve', 'user' => $user->id];
        $url->addQuery('status', 'success');
        $url->addQuery('sign', $client->sign($action));
        $url->addQuery('user', $user->id);

        return redirect((string) $url, 302);
    }
}
