<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriberController extends Controller
{
	public function Subscribe(Request $request)
	{
		$mailchimp = new \MailchimpMarketing\ApiClient();
		$mailchimp->setConfig([
			'apiKey' => env('MAILCHIMP_API_KEY', 'ABC123'),
			'server' => 'us18'
		]);

		$list_id = "9db39916ef"; //mixmav list

		try {
			$response = $mailchimp->lists->setListMember($list_id, md5(strtolower($request->email)),[
				"email_address" => $request->email,
				"status_if_new" => "subscribed"
			]);

			return true;
		} catch (MailchimpMarketing\ApiException $e) {
			return array('errors' => 'Something went wrong.');
		}
	}
}
