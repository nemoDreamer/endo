<?php
/**
* AppUrl
*
* Provides basic routing
* (See Url for default routes)
*
* @author Philip Blyth
*/
class AppUrl extends Url
{
  static $routes = array(

    // (some examples of what can be done)

    // HARD
    // --------------------------------------------------
    'demo/.*' => array(
      'redirect' => 'http://resources.DOMAIN/demo'
    ),

    // MEMBERS
    // --------------------------------------------------
    // - root
    'members/' => array(
      'replace' => 'app_dashboards/members',
      'is_members' => true
    ),
    // - general
    'members/(.*)/(.*)' => array(
      'replace' => '$1/members_$2',
      'is_members' => true,
      'continue' => true
    ),

    // TRANSLATIONS
    // --------------------------------------------------
    // - profile
    'profile/(.*)' => array(
      'replace' => 'listings/$1'
    ),
    // - manual
    'manual/members_show/\d+/(\d+)/(.*)' => array(
      'replace' => 'manual_pages/members_show/$1/$2'
    ),
    'manual/(.*)' => array(
      'replace' => 'manual_chapters/$1'
    ),
    // - seminars
    'seminars/members_show/\d+/(\d+)/(.*)' => array(
      'replace' => 'seminar_talks/members_show/$1/$2'
    ),
    'seminars/(.*)' => array(
      'replace' => 'seminar_speakers/$1'
    ),
    // - lesson resources
    'lessons/members_show/\d+/(\d+)/(.*)' => array(
      'replace' => 'lesson_sources/members_show/$1/$2'
    ),
    // - events
    'events/(.*)' => array(
      'replace' => 'app_events/$1'
    ),
    // - account
    'account/(.*)' => array(
      'replace' => 'app_users/$1'
    ),

    // GETSTARTED
    // --------------------------------------------------
    // - getstarted
    'getstarted/((?:basic|plus|pro)/)(step.*)?/?' => array(
      'replace' => 'getstarted/$2/$1'
    )
  );
}

?>
