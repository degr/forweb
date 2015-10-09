        if($this-><?= $data['propertyName'] ?> === null){
            $this-><?= $data['propertyName'] ?> = ORM::loadBinded('<?=
        $data['bind']->getRightTable()->getName()?>$this->get<?= 
        ucfirst($data['bind']->getLeftKey()).$data['postfix']?>(), '<?=
        $data['bind']->getLeftKey()?>', '<?=
        $data['bind']->getRightKey() ?>', '<?=
        $data['bind']->getType() ?>');
        }
