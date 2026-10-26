<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;

/*
 * Login and ownership checks for controllers that act on a profile, assignment or file by id.
 * Expects $authentication, $profile and $assignment on the controller, and $annotation for file checks.
 */
trait ChecksUserOwnership
{

    protected function checkUserOwnsProfile($profile_id)
    {
        $keycloak_user = $this->loggedInUser();
        self::abortUnlessGuid($profile_id);

        return parent::checkUserIsAuthorized($keycloak_user, $this->profile->get($profile_id));
    }

    protected function checkUserOwnsAssignment($assignment_id)
    {
        return $this->checkAssignmentBelongsToUser($this->loggedInUser(), $assignment_id);
    }

    protected function checkUserOwnsFile($annotation_id)
    {
        $keycloak_user = $this->loggedInUser();
        self::abortUnlessGuid($annotation_id);

        // a file is owned through the assignment it is attached to
        $annotation = $this->annotation->get($annotation_id);

        return $this->checkAssignmentBelongsToUser($keycloak_user, $annotation['object_id']);
    }

    private function checkAssignmentBelongsToUser($keycloak_user, $assignment_id)
    {
        self::abortUnlessGuid($assignment_id);

        $assignment = $this->assignment->get($assignment_id);

        parent::checkUserIsAuthorized($keycloak_user, $this->profile->get($assignment['contact_id']));

        return $assignment;
    }

    private function loggedInUser()
    {
        try
        {
            $keycloak_user = $this->authentication->user();
        }
        catch (ClientException $exception)
        {
            // Keycloak rejects a missing or expired token
            $keycloak_user = null;
        }

        if ( ! $keycloak_user)
        {
            abort(401, 'unauthorized');
        }

        return $keycloak_user;
    }

    // ids are put into ecas-api urls and Dynamics queries, so only accept GUIDs
    private static function abortUnlessGuid($id)
    {
        if ( ! Str::isUuid($id))
        {
            abort(404, 'not found');
        }
    }

}
