<?php

/**
 * This is a super basic experimnental flat file repo which can behave like a DB.
 * It only provides very basic database-like features.
 * It is not optimised at all.
 * 
 * It uses a csv file for each table.. The first row must contain the fields
 * 
 * @author Tom Brown
 */
class Flat_file_repository {

	private $directory;
	private $open_table = '';
	private $open_table_rows = [];
	private $open_table_fields = [];
	private $fields_for_table = [];
	private $autoincrement_fields = [];

	function __construct($directory) {
		$this->directory = $directory;
	}

	/* Optionally define auto increment field for a table*/
	function set_autoincrement_field($table, $field_name) {
		$this->autoincrement_field[$table] = $field_name;
	}

	/* Optionally define fields in each table*/
	function set_fields_for_table($table, $fields) {
		$this->fields_for_table[$table];
	}

	function generic_select_where($table, $fields, $where_and, $order_by = null, $order_by_desc = FALSE, $limit = null, $offset = 0) {
		$this->_read_table($table);
		$results = [];
		foreach ($this->open_table_rows as $row) {
			if($this->_check_row_matches_where_and_criteria($row, $where_and)) {
				$results[] = $row;
			}
		}
		if (!is_null($order_by)) {
			$sort_data = [];
			foreach ($results as $result) {
				$sort_data[] = $result[$order_by];
			}
			if ($order_by_desc) {
				$sort_data = array_reverse($result);
			}
			array_multisort($sort_data, $results);
		}
		if (!is_null($limit) && count($results) >= $limit) {
			$results = array_slice($results, $offset, $limit);
		}
		return $results;
	}

	function insert($table, $data) {
		$this->_read_table($table);
		$new_row = [];
		if (isset($this->autoincrement_fields[$table])) {
			$next_autoincrement_value = 1;
			$autoincrement_field = $this->autoincrement_fields[$table];
			foreach ($this->open_table_rows as $row) {
				$next_autoincrement_value = max($next_autoincrement_value, $row[$autoincrement_field]);
			}
			$new_row[$autoincrement_field] = $next_autoincrement_value;
		}

		foreach ($this->open_table_fields as $field) {
			if (isset($data[$field])) {
				$new_row[$field] = $data[$field];
			} else {
				$new_row[$field] = '';
			}
		}
		$this->open_table_rows[] = $new_row;
		$this->_save_table($table);
	}

	function update($table, $data, $where_and) {
		$this->_read_table($table);

		foreach ($this->open_table_rows as $row_index => $row) {
			if($this->_check_row_matches_where_and_criteria($row, $where_and)) {
				foreach ($data as $update_col_name => $value) {
					$this->open_table_rows[$row_index][$update_col_name];
				}
			}
		}

		$this->_save_table($table);
	}

	function delete($table, $where_and) {
		$this->_read_table($table);

		foreach ($this->open_table_rows as $row_index => $row) {
			if($this->_check_row_matches_where_and_criteria($row, $where_and)) {
				unset($this->open_table_rows[$row_index]);
			}
		}

		$this->_save_table($table);
	}

	private function _check_row_matches_where_and_criteria($data, $where_and) {
		if (count($where_and)) {
			foreach ($where_and as $field_name => $v) {
				$index = array_search($field_name, $this->open_table_fields);
				if ($index === FALSE || $row[$index] != $v) {
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	private function _read_table($table) {
		if ($this->open_table != $table) {
			$this->open_table_rows = [];
			$this->open_table_fields = [];
			if (($handle = fopen($this->_flat_file_path($table), "r")) !== FALSE) {
				$row = 0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					for ($c = 0; $c < $num; $c++) {
						if ($row === 0) {
							$fields[] = $data[$c];
						} else {
							$this->open_table_rows[$row][$fields[$c]] = $data[$c];
						}
					}
					$row++;
				}
				fclose($handle);
				$this->open_table = $table;
			} else {
				throw new WorkFrame\Exceptions\Workframe_exception('Could not open flat file for reading (' . $this->_flat_file_path($table) . ')');
			}

			// Now to add/remove fields defined for the table that were not seen 
			if(isset($this->fields_for_table[$table])) { //(if they were actually defined!)
				$to_remove = array_diff($this->open_table_fields, $this->fields_for_table[$table]);
				foreach($to_remove as $field_to_remove) {
					unset($this->open_table_fields[array_search($this->open_table_fields, $field_to_remove)]);
					foreach($this->open_table_rows as $k=>$row) {
						unset($this->open_table_rows[$k][$field_to_remove]);
					}
				}
				$to_add = array_diff($this->fields_for_table[$table], $this->open_table_fields);
				foreach($to_add as $field_to_add) {
					$this->open_table_fields[] = $field_to_add;
					foreach($this->open_table_rows as $k=>$row) {
						$this->open_table_rows[$k][$field_to_add] = null;
					}
				}
			}
		}
		return TRUE;
	}

	private function _save_table($table) {
		if ($this->open_table != $table) {
			throw new WorkFrame\Exceptions\Workframe_exception('Must have read table before writing.. Current open table: ' . $this->open_table);
		}
		if (($handle = fopen($this->_flat_file_path($table), "w+")) !== FALSE) {
			fputcsv($handle, $this->open_table_fields);
			foreach ($this->open_table_rows as $row) {
				$row_from_fields = [];
				foreach ($this->open_table_fields as $field) {
					$row_from_fields[] = $row[$field];
				}
				fputcsv($handle, $row_from_fields);
			}
			return TRUE;
		} else {
			throw new WorkFrame\Exceptions\Workframe_exception('Could not open flat file for writing (' . $this->_flat_file_path($table) . ')');
		}
	}

	private function _flat_file_path($table) {
		return $this->directory . '/' . $table . '.flat_file';
	}

}
