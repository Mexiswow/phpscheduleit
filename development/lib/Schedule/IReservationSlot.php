<?php

interface IReservationSlot
{
	/**
	 * @return Time
	 */
	public function Begin();
	
	/**
	 * @return Time
	 */
	public function End();
	
	/**
	 * @return int
	 *
	 */
	public function PeriodSpan();	
	
	/**
	 * @param string $timezone
	 * @return IReservationSlot
	 */
	public function ToTimezone($timezone);
}

?>