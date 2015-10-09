        if($this-><?= $data['propertyName'] ?> === null){
            $this-><?= $data['propertyName'] ?> = DB::getCell("select <?= $data['field']->getName()
                ." from ". $data['table']->getName()." where "
                .$data['table']->getPrimaryKey() ?> = '".DB::escape($this->getPrimaryKey())."'");
        }
