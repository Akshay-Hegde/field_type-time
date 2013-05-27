<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Time field type for PyroCMS
 *
 * @author		Marco Grüter
 */
class Field_time
{
	public $field_type_slug			= 'time';

	public $db_col_type				= 'time';

	public $custom_parameters		= array('use_seconds', 'minutes_interval', 'seconds_interval');

	public $version					= '1.0';

	public $author					= array('name'=>'Healthworld (Schweiz) AG', 'url'=>'http://www.healthworld.ch');

    public function pre_save($input, $field)
	{
		// input data without form field
		if (isset($input) && !empty($input) && $input !== '1')
		{
			return $input;
		}

		$value = '';

		$hours = '00';
		$minutes = '00';

		// hours
		if ($this->CI->input->post($field->field_slug.'_hours'))
		{
			$hours = $this->CI->input->post($field->field_slug.'_hours');
		}

		// minutes
		if ($this->CI->input->post($field->field_slug.'_minutes'))
		{
			$minutes = $this->CI->input->post($field->field_slug.'_minutes');
		}

		$value = $hours . ':' . $minutes;

		if( ! empty($field->field_data['use_seconds']) && $field->field_data['use_seconds'] == 'yes')
		{
			// Minute
			$seconds = '00';
			if ($this->CI->input->post($field->field_slug.'_seconds'))
			{
				$seconds = $this->CI->input->post($field->field_slug.'_seconds');
			}

			$value .= ':' . $seconds;
		}

		return $value;
	}

	public function form_output($data, $entry_id, $field)
	{
		$current_time = $this->_parse_time($data['value'], $data['form_slug']);

		$minutes_interval = empty($data['custom']['minutes_interval']) ? 5 : $data['custom']['minutes_interval'];
		$seconds_interval = empty($data['custom']['seconds_interval']) ? 10 : $data['custom']['seconds_interval'];

		$form_input = '';

		// build the hours array and form input
		$hours = array();

		for($x = 0;$x <= 23; $x += 1)
		{
			$hours[$x] = $this->_two_digit_number($x);
		}

		$form_input = form_dropdown($data['form_slug'] . '_hours', $hours, $current_time['hours']);

		// build the minutes array
		$minutes = array();

		for($x = 0;$x <= 59; $x += $minutes_interval)
		{
			$minutes[$x] = $this->_two_digit_number($x);
		}

		$form_input .= form_dropdown($data['form_slug'] . '_minutes', $minutes, $current_time['minutes']);

		// build the seconds array, if they want to

		if( isset($data['custom']['use_seconds']) && $data['custom']['use_seconds'] == 'yes')
		{
			for($x = 0;$x <= 59; $x += $seconds_interval)
			{
				$seconds[$x] = $this->_two_digit_number($x);
			}

			$form_input .= form_dropdown($data['form_slug'] . '_seconds', $seconds, $current_time['seconds']);
		}


		// add a dummy time value, so the required rule is happy
		$form_input .= form_hidden($data['form_slug'], '1');

		return $form_input;
	}

	public function param_minutes_interval($value = null)
	{
		$options = array(1 => 1, 5 => 5, 10 => 10, 15 => 15, 30 => 30);

		return form_dropdown('minutes_interval', $options, $value);
	}

	public function param_seconds_interval($value = null)
	{
		$options = array(1 => 1, 5 => 5, 10 => 10, 15 => 15, 30 => 30);

		return form_dropdown('seconds_interval', $options, $value);
	}

	public function param_use_seconds($value = null)
	{
		$options = array('yes' => 'Yes', 'no' => 'No');

		return form_dropdown('use_seconds', $options, $value);
	}

	private function _parse_time($value, $slug)
	{
		$default_time = array('hours' => 12, 'minutes' => 0, 'seconds' => '0');
		$time = array();

		if($value == '' && $this->CI->input->post($slug) == '1')
		{
			$value = $this->CI->input->post($slug . '_hours') . ':' . $this->CI->input->post($slug . '_minutes')  . ':' . $this->CI->input->post($slug . '_seconds');
		}

		list($time['hours'], $time['minutes'], $time['seconds']) = explode(':', $value);

		return array_merge($default_time, $time);
	}

	/**
	 * Turns a single digit number into a
	 * two digit number - from datetime field_type. Thank you!
	 *
	 * @access 	public
	 * @param 	string
	 * @return 	string
	 */
	private function _two_digit_number($digit)
	{
		$digit = trim($digit);

		switch(strlen($digit))
		{
			case 0:
				return '00';
			break;

			case 1:
				return '0' . $digit;
			break;

			default:
				return $digit;
		}
	}

}