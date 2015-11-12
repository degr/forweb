    /**
     * `<?= $data['name']?>` field setter 
     * @var <?= $data['persistClassName'].$data['typePrefix']." $".$data['name'] ?> 
     * @return <?= $data['bind']->getLeftTable()->getPersistClassName() ?> 
     */
    public function set<?= ucfirst($data['name']) ?>($<?=$data['name']?>){
        $this-><?= $data['name'] ?> = $<?= $data['name'] ?>;
        return $this;
    }
