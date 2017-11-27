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
        $time = (int) $request->query('time');
        $action = ['app' => $client->id, 'action' => 'auth/resolve', 'time' => $time];
        $url = new URL($redirect);

        // 过期 五分钟 5 * 60
        if ((time() - $time) > 300) {
            $url->addQuery('status', 'fail');
            $url->addQuery('code', '10000');

            return redirect((string) $url, 302);
        }

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

        $action = [
            'app' => $client->id,
            'action' => 'auth/resolve',
            'user' => $user->id,
            'time' => $time = time(),
        ];
        $url->addQuery('status', 'success');
        $url->addQuery('sign', $client->sign($action));
        $url->addQuery('user', $user->id);
        $url->addQuery('time', $time);

        return redirect((string) $url, 302);
    }
}
