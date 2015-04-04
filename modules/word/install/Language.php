<?

class Word_Install_Language{
 public function install(){
     $lIdsQuery = "SELECT id, locale FROM languages ";
     $out = DB::getAssoc($lIdsQuery, 'locale', 'id');
     if(empty($out)) {
         $query = "INSERT INTO languages (locale, is_default) VALUES"
             . "('en', 1), ('ru', 0)";
         DB::query($query);
         $out = DB::getAssoc($lIdsQuery, 'locale', 'id');
     }
     return $out;
 }
}