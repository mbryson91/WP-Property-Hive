<?php

if ( ! defined( 'ABSPATH' ) )
{
	// Exit if accessed directly
	exit;
}

class PH_Key_Date {

	const UPCOMING_THRESHOLD = '+ 7 DAYS';
	const OVERDUE_THRESHOLD = 'Today';

	/** @var int */
	public $id;

	/** @var string */
	private $description;

	public function __construct( WP_Post $post ) {
		$this->id = $post->ID;
		$this->description = $post->post_title;
	}

	public function description() {
		return $this->description;
	}

	public function property() {
		return new PH_Property(get_post($this->_property_id));
	}

	public function tenancy() {
		return new PH_Tenancy($this->_tenancy_id);
	}

	public function date_due() {
		return new DateTime($this->_date_due);
	}

	public function status() {

		switch ($this->_key_date_status)
		{
			case 'pending':
				switch(true)
				{
					case $this->date_due() <= new DateTime(PH_Key_Date::OVERDUE_THRESHOLD):
						return 'overdue';
					case $this->date_due() <= new DateTime(PH_Key_Date::UPCOMING_THRESHOLD):
						return 'upcoming';
					default:
						return 'pending';
				}
			case 'booked':
			case 'complete':
			default:
				return $this->_key_date_status;
		}
	}

	/**
	 * __isset function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( ! $this->id ) {
			return false;
		}
		return metadata_exists( 'post', $this->id, '_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		// Get values or default if not set
		$value = get_post_meta( $this->id, $key, true );
		if ($value == '')
		{
			$value = get_post_meta( $this->id, '_' . $key, true );
		}
		return $value;
	}
}
