    /**
     * Primary key getter
     */
    public function getPrimaryKey(){
        return $this-><?= $data['field']->getName().$data['postfix'] ?>;
    }