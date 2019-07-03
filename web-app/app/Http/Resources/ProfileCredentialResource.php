<?php

namespace App\Http\Resources;

use App\Dynamics\Decorators\CacheDecorator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProfileCredentialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $repository             = env('DATASET') == 'Dynamics' ? 'Dynamics' : 'MockEntities\Repository';
        $credentials            = ( new CacheDecorator(App::make('App\\' . $repository .'\Credential')))->all();

        return [
            'id'            => $this['id'],
            'user_id'       => $this['user_id'],
            'credential'    => $credentials->firstWhere('id', $this['credential_id']),
            'verified'      => (Boolean) $this['verified']
        ];
    }
}
