<?php
namespace jumper;

trait googleAnalyticsTrait 
{

	public static function gaGetFirstprofileId(\Google_Service_Analytics &$analytics) 
	{
	  // Get the user's first view (profile) ID.

	  // Get the list of accounts for the authorized user.
	  $accounts = $analytics->management_accounts->listManagementAccounts();

	  if (count($accounts->getItems()) > 0) {
		$items = $accounts->getItems();
		$firstAccountId = $items[0]->getId();

		// Get the list of properties for the authorized user.
		$properties = $analytics->management_webproperties
			->listManagementWebproperties($firstAccountId);

		if (count($properties->getItems()) > 0) {
		  $items = $properties->getItems();
		  $firstPropertyId = $items[0]->getId();

		  // Get the list of views (profiles) for the authorized user.
		  $profiles = $analytics->management_profiles
			  ->listManagementProfiles($firstAccountId, $firstPropertyId);

		  if (count($profiles->getItems()) > 0) {
			$items = $profiles->getItems();

			// Return the first view (profile) ID.
			return $items[0]->getId();

		  } else {
			throw new \Exception('No views (profiles) found for this user.');
		  }
		} else {
		  throw new \Exception('No properties found for this user.');
		}
	  } else {
		throw new \Exception('No accounts found for this user.');
	  }
	}

	public static function gaGetResults(\Google_Service_Analytics &$analytics, $profileId) 
	{
	  // Calls the Core Reporting API and queries for the number of sessions
	  // for the last seven days.
	   return $analytics->data_ga->get(
		   'ga:' . $profileId,
		   '30daysAgo',
		   'today',
		   'ga:sessions');
	}

	public static function gaPrintResults(\Object &$results) 
	{
	  // Parses the response from the Core Reporting API and prints
	  // the profile name and total sessions.
	  if (count($results->getRows()) > 0) {

		// Get the profile name.
		$profileName = $results->getProfileInfo()->getProfileName();

		// Get the entry for the first entry in the first row.
		$rows = $results->getRows();
		$sessions = $rows[0][0];

		// Print the results.
		print "First view (profile) found: $profileName\n";
		print "Total sessions: $sessions\n";
	  } else {
		print "No results found.\n";
	  }
	}	
	
}
