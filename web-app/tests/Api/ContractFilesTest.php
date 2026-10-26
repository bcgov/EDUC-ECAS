<?php

namespace Tests\Api;

use App\Dynamics\Annotation;
use App\Dynamics\Assignment;
use App\Keycloak\KeycloakGuard;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\UploadedFile;
use Tests\BaseMigrations;


class ContractFilesTest extends BaseMigrations
{

    const CALLER_FEDERATED_ID   = 'caller_federated_id';
    const PROFILE_ID            = '11111111-1111-1111-1111-111111111111';
    const ASSIGNMENT_ID         = '22222222-2222-2222-2222-222222222222';
    const ANNOTATION_ID         = '33333333-3333-3333-3333-333333333333';

    private $ecas_api_requests = [];


    public function endpoints()
    {
        return [
            'portal assignments'    => ['GET',    self::PROFILE_ID,    'portalassignment',  'profile'],
            'list uploaded files'   => ['GET',    self::ASSIGNMENT_ID, 'listuploadedfiles', 'assignment'],
            'contract file'         => ['GET',    self::ASSIGNMENT_ID, 'contractfile',      'assignment'],
            'file upload'           => ['POST',   self::ASSIGNMENT_ID, 'fileupload',        'assignment'],
            'file submit'           => ['PATCH',  self::ASSIGNMENT_ID, 'filesubmit',        'assignment'],
            'file download'         => ['GET',    self::ANNOTATION_ID, 'filedownload',      'file'],
            'file delete'           => ['DELETE', self::ANNOTATION_ID, 'filedelete',        'file'],
        ];
    }


    /**
     * @test
     * @dataProvider endpoints
     */
    public function an_unauthenticated_user_is_rejected($method, $id, $endpoint)
    {
        $this->withExceptionHandling();
        $this->mockUnauthenticatedUser();
        $this->mockEcasApi();

        $this->json($method, '/api/' . $id . '/' . $endpoint)->assertStatus(401);

        $this->assertEmpty($this->ecas_api_requests);
    }


    /**
     * @test
     * @dataProvider endpoints
     */
    public function a_token_keycloak_rejects_is_rejected($method, $id, $endpoint)
    {
        $this->withExceptionHandling();
        $this->mockKeycloakRejectingToken();
        $this->mockEcasApi();

        $this->json($method, '/api/' . $id . '/' . $endpoint)->assertStatus(401);

        $this->assertEmpty($this->ecas_api_requests);
    }


    /**
     * @test
     * @dataProvider endpoints
     */
    public function a_user_cannot_use_records_owned_by_someone_else($method, $id, $endpoint, $id_type)
    {
        $this->withExceptionHandling();
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy('another_federated_id', $id_type);
        $this->mockEcasApi();

        $this->json($method, '/api/' . $id . '/' . $endpoint)->assertStatus(401);

        $this->assertEmpty($this->ecas_api_requests);
    }


    /**
     * @test
     * @dataProvider endpoints
     */
    public function an_id_that_is_not_a_guid_is_rejected($method, $id, $endpoint)
    {
        $this->withExceptionHandling();
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockEcasApi();

        $this->json($method, '/api/' . $id . ')/' . $endpoint)->assertStatus(404);

        $this->assertEmpty($this->ecas_api_requests);
    }


    /** @test */
    public function a_user_can_get_their_portal_assignments()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'profile');
        $this->mockEcasApi([$this->jsonResponse(['value' => [[
            'educ_assignmentid'                                     => self::ASSIGNMENT_ID,
            'startDate@OData.Community.Display.V1.FormattedValue'   => '2026-07-06',
            'endDate@OData.Community.Display.V1.FormattedValue'     => '2026-07-10'
        ]]])]);

        $this->get('/api/' . self::PROFILE_ID . '/portalassignment')
            ->assertOk()
            ->assertJsonFragment(['EducAssignmentId' => self::ASSIGNMENT_ID]);

        $this->assertEcasApiRequest('GET', '/EcasPortalAssignments?contactId=' . self::PROFILE_ID);
    }


    /** @test */
    public function a_user_can_list_files_uploaded_to_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'assignment');
        $this->mockEcasApi([$this->jsonResponse(['value' => [[
            'annotationid'  => self::ANNOTATION_ID,
            'filename'      => 'signed.pdf'
        ]]])]);

        $this->get('/api/' . self::ASSIGNMENT_ID . '/listuploadedfiles')
            ->assertOk()
            ->assertJsonFragment(['AnnotationId' => self::ANNOTATION_ID]);

        $this->assertEcasApiRequest('GET', '/ContractFiles/ListUploadedFiles?assignmentId=' . self::ASSIGNMENT_ID);
    }


    /** @test */
    public function a_user_can_get_the_contract_file_for_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'assignment');
        $this->mockEcasApi([$this->jsonResponse(['value' => [[
            'annotationid'  => self::ANNOTATION_ID,
            'filename'      => 'contract.pdf'
        ]]])]);

        $this->get('/api/' . self::ASSIGNMENT_ID . '/contractfile')
            ->assertOk()
            ->assertJsonFragment(['AnnotationId' => self::ANNOTATION_ID]);

        $this->assertEcasApiRequest('GET', '/ContractFiles/ContractFile?assignmentId=' . self::ASSIGNMENT_ID);
    }


    /** @test */
    public function a_user_can_upload_a_file_to_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'assignment');
        $this->mockEcasApi([$this->jsonResponse('new_annotation_id')]);

        $this->post('/api/' . self::ASSIGNMENT_ID . '/fileupload', [
                'file' => UploadedFile::fake()->create('signed.pdf', 1)
            ])
            ->assertOk()
            ->assertJson(['UploadFile' => 'new_annotation_id']);

        $request = $this->assertEcasApiRequest('POST', '/ContractFiles/UploadFile');
        $sent = json_decode($request->getBody(), true);
        $this->assertEquals('signed.pdf', $sent['filename']);
        $this->assertEquals('/educ_assignments(' . self::ASSIGNMENT_ID . ')', $sent['objectid_educ_assignment@odata.bind']);
    }


    /** @test */
    public function a_user_can_submit_files_for_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'assignment');
        $this->mockEcasApi([new Response(204)]);

        $this->patch('/api/' . self::ASSIGNMENT_ID . '/filesubmit')
            ->assertOk();

        $request = $this->assertEcasApiRequest('PATCH', '/Operations?statement=educ_assignments(' . self::ASSIGNMENT_ID . ')');
        $this->assertEquals(['educ_contractstage' => '610410002'], json_decode($request->getBody(), true));
    }


    /**
     * The controller calls header() directly, which fails once phpunit has printed anything
     *
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function a_user_can_download_a_file_attached_to_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'file');
        $this->mockEcasApi([$this->jsonResponse(['value' => [[
            'filename'      => 'contract.pdf',
            'documentbody'  => base64_encode('contract file content')
        ]]])]);

        // the controller streams the file straight to output
        $this->expectOutputString('contract file content');

        $this->get('/api/' . self::ANNOTATION_ID . '/filedownload')
            ->assertOk();

        $this->assertEcasApiRequest('GET', '/ContractFiles/GetFile?annotationid=' . self::ANNOTATION_ID);
    }


    /** @test */
    public function a_user_can_delete_a_file_attached_to_their_assignment()
    {
        $this->mockUser(self::CALLER_FEDERATED_ID);
        $this->mockRecordsOwnedBy(self::CALLER_FEDERATED_ID, 'file');
        $this->mockEcasApi([$this->jsonResponse('The contract has been removed')]);

        $this->delete('/api/' . self::ANNOTATION_ID . '/filedelete')
            ->assertOk()
            ->assertJson(['ContractFile' => 'The contract has been removed']);

        $this->assertEcasApiRequest('DELETE', '/ContractFiles/RemoveFile?annotationid=' . self::ANNOTATION_ID);
    }


    /*
     * Mock the Dynamics records linking an id to its owner: file -> assignment -> profile
     */
    protected function mockRecordsOwnedBy($federated_id, $id_type)
    {
        if ($id_type == 'file') {
            $this->mockGetAnnotation(self::ANNOTATION_ID, self::ASSIGNMENT_ID);
        }

        if ($id_type != 'profile') {
            $this->mockGetAssignment(self::ASSIGNMENT_ID, self::PROFILE_ID);
        }

        $this->mockGetProfile(self::PROFILE_ID, $this->validProfileData([
            'federated_id'  => $federated_id
        ]));
    }


    /*
     * The real guard throws when Keycloak's userinfo endpoint rejects a missing or expired token
     */
    protected function mockKeycloakRejectingToken()
    {
        $guard = \Mockery::mock(KeycloakGuard::class);
        $guard->shouldReceive('user')
            ->once()
            ->andThrow(new ClientException('401 Unauthorized', new Request('GET', 'userinfo'), new Response(401)));

        $this->app->instance(KeycloakGuard::class, $guard);
    }


    protected function mockGetAssignment($assignment_id, $contact_id)
    {
        $repository = \Mockery::mock(Assignment::class);
        $repository->shouldReceive('get')
            ->with($assignment_id)
            ->once()
            ->andReturn([
                'id'            => $assignment_id,
                'contact_id'    => $contact_id
            ]);

        $this->app->instance(Assignment::class, $repository);
    }


    protected function mockGetAnnotation($annotation_id, $object_id)
    {
        $repository = \Mockery::mock(Annotation::class);
        $repository->shouldReceive('get')
            ->with($annotation_id)
            ->once()
            ->andReturn([
                'id'            => $annotation_id,
                'object_id'     => $object_id
            ]);

        $this->app->instance(Annotation::class, $repository);
    }


    /*
     * Replace the Guzzle client the controllers use to call ecas-api, recording each request
     */
    protected function mockEcasApi(array $responses = [])
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($this->ecas_api_requests));

        $this->app->instance(Client::class, new Client(['handler' => $handler]));
    }


    protected function assertEcasApiRequest($method, $path_and_query)
    {
        $this->assertCount(1, $this->ecas_api_requests);

        $request = $this->ecas_api_requests[0]['request'];
        $this->assertEquals($method, $request->getMethod());
        $this->assertStringEndsWith($path_and_query, (string) $request->getUri());

        return $request;
    }


    private function jsonResponse($body)
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($body));
    }

}
