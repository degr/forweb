    /**
     * `<?= $data['name'] ?>` field getter
     * @return <?= $data['type'] ?> $<?= $data['name']?> 
     */
    public function get<?= ucfirst($data['name'])?>(){
<?= $data['lazyLoadText'] ?>
        return $this-><?= $data['name'] ?>;
    }