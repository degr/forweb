    /**
     * `<?= $data['name']?>` field getter
     * @return <?= $data['bind']->getRightTable()->getPersistClassName().$typePrefix ?> 
     */
    public function get<?= ucfirst($data['bind']->getLeftField()) ?>(){
<?php if ($data['bind']->getLazyLoad()) { echo $data['lazyLoadText']; } ?>
        return $this-><?= $data['bind']->getLeftField() ?>;
    }
