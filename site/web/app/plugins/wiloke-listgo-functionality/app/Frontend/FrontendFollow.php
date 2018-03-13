<?php
namespace WilokeListGoFunctionality\Frontend;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing as WilokeFollowingTbl;

class FrontendFollow{
	public static $redisFollowing = 'wiloke_listgo_following';
	public static $redisFollower = 'wiloke_listgo_follower';

	public static function getFollowers($userID){
		$aFollowers = array();
		if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(self::$redisFollower) ){
			$aGetFollowers = \Wiloke::hGet(self::$redisFollower, $userID, true);
			if ( !empty($aGetFollowers) ){
				foreach ( $aGetFollowers as $key => $userID ){
					$aFollowers[$key] = \Wiloke::hGet(\WilokeUser::$redisKey, $userID, true);
				}
			}
		}else{
			global $wpdb;
			$tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . WilokeFollowingTbl::$tblName;

			$aFollowers = $wpdb->get_results(
				$wpdb->prepare(
				"SELECT $tblUser.display_name, $tblFollowing.follower_ID as ID FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.follower_ID = $tblUser.ID) WHERE $tblFollowing.user_ID = %d",
					$userID
				),
				ARRAY_A
			);
		}

		return $aFollowers;
	}

	public static function getFollowing($userID){
		$aFollowing = array();
		if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(self::$redisFollowing) ){
			$aGetFollowing = \Wiloke::hGet(self::$redisFollowing, $userID, true);
			if ( !empty($aGetFollowing) ){
				foreach ( $aGetFollowing as $key => $userID ){
					$aFollowing[$key] = \Wiloke::hGet(\WilokeUser::$redisKey, $userID, true);
				}
			}
		}else{
			global $wpdb;
			$tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . WilokeFollowingTbl::$tblName;

			$aFollowing = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT $tblUser.display_name, $tblFollowing.user_ID as ID FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.user_ID = $tblUser.ID) WHERE $tblFollowing.follower_ID = %d",
					$userID
				),
				ARRAY_A
			);
		}

		return $aFollowing;
	}
}