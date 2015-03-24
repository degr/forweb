<?
abstract class ORM_Objects_PersistBase implements ORM_Objects_IPersitBase{
	public function toArray($recursive = false){
		$vars = get_object_vars($this);
		foreach($vars as $key => $value){
			$type = gettype($value);
			switch($type){
				case 'array':
					if($recursive) {
						foreach($value as $item){
							$vars[$key] = $item->toArray();
						}
					}else{
						unset($vars[$key]);
					}
				break;
				case 'object':
					if($recursive) {
						$vars[$key] = $value->toArray();
					} else {
						unset($vars[$key]);
					}
					break;
			}
		}
		return $vars;
	}
}