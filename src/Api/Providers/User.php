<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class User extends Provider
{
    use UploadsImages;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'invite',
        'profile',
        'username',
        'isBanned',
        'deactivate',
        'convertToBusiness',
    ];

    const ACCOUNT_TYPE_OTHER = 'other';
    const REGISTRATION_COMPLETE_EXPERIENCE_ID = '11:10105';

    /**
     * Updates or returns user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo If empty returns current user profile.
     *
     * @return bool|array
     */
    public function profile($userInfo = [])
    {
        if(empty($userInfo)) {
            return $this->execGetRequest([], UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        }

        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->execPostRequest($userInfo, UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS);
    }

    /**
     * Convert your account to a business one.
     *
     * @param string $businessName
     * @param string $websiteUrl
     * @return bool
     */
    public function convertToBusiness($businessName, $websiteUrl = '')
    {
        $data = [
            'business_name' => $businessName,
            'website_url'   => $websiteUrl,
            'account_type'  => self::ACCOUNT_TYPE_OTHER,
        ];

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS);
    }

    /**
     * Checks if current user is banned
     *
     * @return bool
     */
    public function isBanned()
    {
        $profile = $this->profile();

       return isset($profile['is_write_banned']) ?
           (bool)$profile['is_write_banned'] :
           false;
    }

    /**
     * Returns current user username
     *
     * @return string
     */
    public function username()
    {
        $profile = $this->profile();

        return isset($profile['username']) ? $profile['username'] : '';
    }

    /**
     * Deactivates your account.
     *
     * @param string $reason
     * @param string $explanation
     * @return bool
     */
    public function deactivate($reason = 'other', $explanation = '')
    {
        $profile = $this->profile();

        if(!isset($profile['id'])) return false;

        $request = [
            'user_id'     => $profile['id'],
            'reason'      => $reason,
            'explanation' => $explanation,
        ];

        return $this->execPostRequest($request, UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT);
    }

    /**
     * Send invite to email
     * @param string $email
     * @return bool|Response
     */
    public function invite($email)
    {
        $data = [
            'email' => $email,
            'type'  => 'email',
        ];

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_INVITE);
    }
}
