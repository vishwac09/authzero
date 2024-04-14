<?php

/**
 * @file
 * Hooks specific to the Authzero module.
 */

use Drupal\user\Entity\User;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Hook invoked just before authzero module validates the user.
 *
 * @param array $authUserDetails
 *   A renderable array representing the user details from authzero.
 *   $user = [
 *     'nickname' => 'abc',
 *     'name' => 'abc',
 *     'picture' => 'url to picture added in auth0',
 *     'updated_at' => 2022-10-01T12:17:27.938Z,
 *     'email' => 'abc@xyz.com',
 *     'email_verified' => 1 || 0,
 *     'iss' => 'Domain of auth0 application',
 *     'sub' => 'auth0|61e3f37eb1392e00699aacc4',
 *     'aud' => 'IJmS0Sw41snjCP2OS5pzAqCI1C1zygzj',
 *     'iat' => '1664626668',
 *     'exp' => '1664626668',
 *     'acr' => 'http://schemas.openid.net/pape/policies/2007/06/multi-factor',
 *     'amr' => [],
 *     'sid' => 8FfPt9kf8aQpgKIZXvEXIcMG0tkXfWM1
 *     'nonce' => ed02edcbcefb71a2902d65f9ab729a14
 *   ];.
 */
function hook_authzero_pre_validate_user(array $authUserDetails = []) {
  if (isset($authUserDetails['email']) && !empty($authUserDetails['email'])) {
    $user = User::create();
    $user->setPassword($authUserDetails['email'] . '.' . time());
    $user->enforceIsNew();
    $user->setEmail($authUserDetails['email']);
    $user->setUsername($authUserDetails['name']);
    $user->set("init", 'mail');
    $user->activate();
    // Save user account.
    $user->save();
  }
}

/**
 * @} End of "addtogroup hooks".
 */
