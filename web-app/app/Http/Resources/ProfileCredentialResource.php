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

        // Dynamics reports credentials as one of: "Yes", "No" or "Unverified"
        // This application transforms the verification field into a Boolean

        return [
            'id'            => $this['id'],
            'contact_id'    => $this['contact_id'],
            'credential'    => new SimpleResource($credentials->firstWhere('id', $this['credential_id'])),
            'verified'      => ($this['verified'] == "Yes") ? TRUE : FALSE,
        ];
    }
}
