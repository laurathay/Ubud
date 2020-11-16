<?php
		
class SPP_Ajax_Jsobj {

	public static function get_js_obj() {
		
		$uids = isset( $_POST['uids'] ) ? $_POST['uids'] : array();
		
		$js_obj_array = array();
		foreach( $uids as $uid ) {
			list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
					'purpose' => 'jsobj from uid',
					'uid' => $uid ) );
			$js_obj = get_transient( $transient_name );
			$js_obj['uid'] = $uid;
			$js_obj_array[] = $js_obj;
		}
		
		header('Content-Type: application/json');
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $js_obj_array, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $js_obj_array );
		}
		echo $ret;
		exit;
	}
	
}
