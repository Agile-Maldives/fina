<?php
namespace App\Http\Controllers;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use App\User;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array
    {
        $userId =  $this->accessToken->getUserIdentifier();
        $currUser = User::where('id','=',$userId)->first();

        return [
            'user_id' => $this->accessToken->getUserIdentifier(),
            'user_name' => $currUser->name,
            'user_email' => $currUser->email,
            'user_role_admin' => $currUser->role_admin,
            'user_role_moderator' => $currUser->role_moderator,
            'user_role_standard' => $currUser->role_standard
        ];
    }
}

