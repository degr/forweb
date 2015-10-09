    /**
     * `<?= $data['name'] ?>` field setter
     * @var <?= $data['type'] ?> $<?= $data['name'].' '.$data['enumValues']?> 
     * @return <?= $data['className'] ?> 
     */
    public function set<?= ucfirst($data['name'])?>($<?=$data['name']?>){
        $this-><?= $data['name']?> = $<?= $data['name'] ?>;
        return $this;
    }
    