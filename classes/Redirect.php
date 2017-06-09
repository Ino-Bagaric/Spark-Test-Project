<?php

class Redirect
{
	public static function to($location = null, $time = 0)
	{
		if ($location) {
			if ($time <= 0) {
				header('location: ' . $location);
			} else {
				header('refresh:' . $time . ';url=' . $location);
			}
			exit;
		}
	}
}
