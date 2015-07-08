<?
abstract class ORM_Persistence_Base implements ORM_Persistence_IBase{
	public function toJson($recursive = false){
		$vars = get_object_vars($this);
		foreach($vars as $key => $value){
			$type = gettype($value);
			switch($type){
				case 'array':
					if($recursive) {
						foreach($value as $item){
							$vars[$key] = $item->toJson();
						}
					}else{
						unset($vars[$key]);
					}
				break;
				case 'object':
					if($recursive) {
						$vars[$key] = $value->toJson();
					} else {
						unset($vars[$key]);
					}
					break;
			}
		}
		return $vars;
	}
}