    /**
     * `<?= $data['name']?>` field setter
     * @var <?= $data['persistClassName'].$typePrefix." $".$name ?>
     * @return <?= $data['bind']->getLeftTable()->getPersistClassName() ?>
     */
    public function set<?= ucfirst($name) ?>($<?=$name?>){
        $this-><?= $name ?> = $<?= ucfirst($name) ?>;
        return $this;;
    }
