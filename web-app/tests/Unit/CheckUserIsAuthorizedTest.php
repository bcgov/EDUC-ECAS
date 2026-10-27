<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;


class CheckUserIsAuthorizedTest extends TestCase
{

    public static function rejectedFederatedIds()
    {
        return [
            'empty on both sides'               => [['federated_id' => ''],     ['federated_id' => '']],
            'user claim missing'                => [['sub' => 'abc'],           ['federated_id' => 'abc']],
            'user empty and profile null'       => [['federated_id' => ''],     ['federated_id' => null]],
            'user null and profile null'        => [['federated_id' => null],   ['federated_id' => null]],
            'profile missing the field'         => [['federated_id' => 'abc'],  []],
            'different ids'                     => [['federated_id' => 'abc'],  ['federated_id' => 'xyz']],
            'loosely equal numeric strings'     => [['federated_id' => '1e3'],  ['federated_id' => '1000']],
        ];
    }


    #[Test]
    #[DataProvider('rejectedFederatedIds')]
    public function a_user_without_a_matching_federated_id_is_rejected(array $keycloak_user, array $profile)
    {
        try {
            $this->checkUserIsAuthorized($keycloak_user, $profile);
        }
        catch (HttpException $exception) {
            $this->assertEquals(401, $exception->getStatusCode());
            return;
        }

        $this->fail('Expected a 401');
    }


    #[Test]
    public function a_user_with_a_matching_federated_id_gets_the_profile()
    {
        $profile = ['id' => 'profile_id', 'federated_id' => 'abc'];

        $this->assertEquals($profile, $this->checkUserIsAuthorized(['federated_id' => 'abc'], $profile));
    }


    private function checkUserIsAuthorized($keycloak_user, array $profile)
    {
        $controller = new class extends Controller {
            public static function check($keycloak_user, array $profile)
            {
                return parent::checkUserIsAuthorized($keycloak_user, $profile);
            }
        };

        return $controller::check($keycloak_user, $profile);
    }

}
