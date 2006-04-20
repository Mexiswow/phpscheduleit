<?php
/**
* A "dumb" object to hold values of a reservation search result
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 04-19-06
* @package 
*
* Copyright (C) 2003 - 2006 phpScheduleIt
* License: GPL, see LICENSE
*/

$basedir = dirname(__FILE__) . '/..';
require_once($basedir . '/lib/Reservation.class.php');

class ReservationResult extends Reservation
{
	function ReservationResult() {
	}
	
	function copyFrom(&$res) {
		$this->id = $res->id;
		$this->start_date = $res->start_date;
		$this->end_date	= $res->end_date;
		$this->start = $res->start;
		$this->end = $res->end;
		$this->resource = $res->resource;
		$this->resource->db = null;
		$this->created = $res->created;
		$this->modified = $res->modified;
		$this->parentid = $res->parentid;
		$this->summary = $res->summary;
		$this->scheduleid = $res->scheduleid;
		$this->is_pending = $res->is_pending;
		$this->is_participant = $res->is_participant;
		$this->reminderid = $res->reminderid;
		$this->reminder_minutes_prior = $res->reminder_minutes_prior;
		$this->users = $res->users;
		$this->user = $res->user;
		$this->user->db = null;
		$this->resources = $res->resources;
	}
}
?>