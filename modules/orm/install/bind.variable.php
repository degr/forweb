    /**
     * persist object field for table: <?= $data['bind']->getRightTable()->getName(); ?>
     * object bind options: $this-><?= $data['bind']->getLeftField().$data['postfix'] ?> on <?= $data['bind']->getRightTable()->getPersistClassName(); ?>-><?= $data['bind']->getRightField(); ?>
     * @var <?=$data['bind']->getRightTable()->getPersistClassName().$data['typePrefix'] ?> $<?=$data['bind']->getLeftField() ?>
     */
    protected $<?= $data['bind']->getLeftField() ?>;
