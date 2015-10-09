    /**
     * `<?= $data['name']?>` field getter
     * @return <?= $data['bind']->getRightTable()->getPersistClassName().$typePrefix ?> 
     */
    public function get<?= ucfirst($data['bind']->getLeftField()) ?>(){
<?php if ($data['bind']->getLazyLoad()) {?>
    <?= $data['lazyLoadText'] ?>;
<?php } ?>
        return $this-><?= $data['bind']->getLeftField() ?>;
    }
