    /**
     * persist object field <?= (!empty($data['enumValues']) ? " enum ['".implode("', '",$data['enumValues'])."']" : "" )
        .($data['field']->getPrimary() ? ", primary key" : "" )
        .($data['field']->getAutoIncrement() ? ", autoincrement" : "" )?> 
     * @var <?= $data['type'] ?> $<?= $data['name']?> 
     */
    protected $<?= $data['name']?>;
