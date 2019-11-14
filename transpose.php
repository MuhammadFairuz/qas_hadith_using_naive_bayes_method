<?php 
	function transpose($data) {
		$temp = array();
		foreach ($data as $key_baris => $value_baris) {
			foreach ($value_baris as $key_kolom => $value_kolom) {
				$temp[$key_kolom][$key_baris] = $value_kolom;
			}
		}
		return $temp;
	}
 ?>