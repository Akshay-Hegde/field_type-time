<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroStreams Date/Time Field Type
 *
 * @package		PyroCMS\Core\Modules\Streams Core\Field Types
 * @author		Parse19
 * @copyright	Copyright (c) 2011 - 2012, Parse19
 * @license		http://parse19.com/pyrostreams/docs/license
 * @link		http://parse19.com/pyrostreams
 */
class Field_time
{	
	public $field_type_slug			= 'time';
	
	public $db_col_type				= 'time';

	public $custom_parameters		= array();

	public $version					= '1.0';

	public $author					= array('name'=>'Healthworld (Schweiz) AG', 'url'=>'http://www.healthworld.ch');
		
	// --------------------------------------------------------------------------

	/**
	 * Validate input
	 *
	 * @access	public
	 * @param	string
	 * @param	string - mode: edit or new
	 * @param	object
	 * @return	mixed - true or error string
	 */
	public function validate($value, $mode, $field)
	{
		// Up front, let's determine if this 
		// a required field.
		$field_data = $this->CI->form_validation->field_data($field->field_slug);
	
		// Determine required
		$rules = $field_data['rules'];
		$rules_array = explode('|', $rules);
		$required = (in_array('required', $rules_array)) ? true : false;

		// Are all three fields available?
		if ( ! $this->CI->input->post($field->field_slug.'_hours') or ! $this->CI->input->post($field->field_slug.'_minutes') )
		{
			return lang('required');
		}

		return true;
	}

	public function form_output($data, $entry_id, $field)
	{
		$date_input = '';

		// get current hour and minutes, seconds get dropped
		if($data['value'] == '')
		{
			$data['value'] = '00:00:00';
		}

		list($current_hours, $current_minutes, $current_seconds) = explode(':', $data['value']);

		// Form input type. Defaults to datepicker
		$input_type = ( ! isset($data['custom']['input_type'])) ? 'datepicker' : $data['custom']['input_type'];

		$hours = array();
		$minutes = array();

		foreach(range(0, 23) as $h)
		{
			$hours[$h] = $this->two_digit_number($h);
		}

		foreach(range(0, 59) as $m)
		{
			$minutes[$m] = $this->two_digit_number($m);
		}

		if ($field->is_required == 'no')
		{
			$hours = array('' => '---')+$hours;
			$minutes = array('' => '---')+$minutes;
		}

		$date_input .= form_dropdown($data['form_slug'].'_hours', $hours, $current_hours);
		$date_input .= form_dropdown($data['form_slug'].'_minutes', $minutes, $current_minutes);

		//hidden field to get around the validation checks
		$date_input .= form_hidden($data['form_slug'], '1');

		return $date_input;
	}

	public function pre_save($input, $field)
	{	
		$time = $this->two_digit_number($this->CI->input->post($field->field_slug.'_hours')). ':'
				. $this->two_digit_number($this->CI->input->post($field->field_slug.'minutes')) . ':00';

		return $time;
	}

	// --------------------------------------------------------------------------

	/**
	 * Turns a single digit number into a
	 * two digit number - from datetime field_type. Thank you!
	 *
	 * @access 	public
	 * @param 	string
	 * @return 	string
	 */
	public function two_digit_number($num)
	{
		$num = trim($num);

		if ($num == '')
		{
			return '00';
		}

		if (strlen($num) == 1)
		{
			return '0'.$num;
		}
		else
		{
			return $num;
		}
	}

}