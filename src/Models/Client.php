<?php

namespace SlimKit\PlusID\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plus_id_clients';

    public function sign(array $action, string $key = ''): string
    {
        ksort($action);
        $action = json_encode($action);

        return md5(hash_hmac('sha256', $action, $key ?: $this->key, true));
    }
}
