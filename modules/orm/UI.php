<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/7/2015
 * Time: 3:53 PM
 */
class ORM_UI
{
    /**
     * @param $fields array [{'table'=>'user', 'field'=>'name'}]
     * @param $values
     * @return array
     */
    public function buildFormByFields($fields){
        /** @var $cache Cache */
        $cache = Core::getModule('Cache');
        $language = Word::getLanguage();
        $cacheKey = json_encode($fields).$language['locale'];
        $out = $cache->load('ORM_UI', $cacheKey);
        if(empty($out)) {
            $out = array();
        } else {
            return $out;
        }

        foreach ($fields as $field) {
            $table = ORM::getTable($field['table']);
            $out[] = $this->buildFormByTable($table, array($table->getField($field['field'])));
        }

        $cache->save($out, 'ORM_UI', $cacheKey);

        return $out;
    }
    /**
     * @param $table ORM_Table
     * @param $fields ORM_Table_Field[]
     * @return array
     */
    public function buildFormByTable($table, $fields = null){
        if($fields == null) {
            $fields = $table->getFields();
        }

        /** @var $cache Cache */
        $cache = Core::getModule('Cache');
        $language = Word::getLanguage();
        $cacheKey = json_encode($fields).$language['locale'];
        $out = $cache->load('ORM_UI', $cacheKey);
        if(empty($out)) {
            $out = array();
        } else {
            return $out;
        }

        $binds = $table->getBinds();
        foreach($fields as $field) {
            switch($field->getType()) {
                case 'boolean':
                case 'bit':
                    $item = $this->buildCheckbox($table, $field);
                    break;
                case 'enum':
                    $item = $this->buildSelectByEnum($table, $field);
                    break;
                case 'text':
                    $item = $this->buildTextarea($table, $field);
                    $autocompleteBind = null;
                    foreach($binds as $bind) {
                        if($bind->getRightKey() == $field->getName()) {
                            $this->setAutocomplete($bind, $item);
                            break;
                        }
                    }
                    break;
                default:
                    $item = $this->buildInput($table, $field);
            }
            $out[] = $item;
        }

        $cache->save($out, 'ORM_UI', $cacheKey);

        return $out;
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    public function buildCheckbox($table, $field)
    {
        return array(
            'type' => 'checkbox',
            'id' => $this->buildId($table, $field),
            'buttons' => array(
                array(
                    'name' => $field->getName(),
                    'data-table' => $table->getName(),
                    'label' => $this->getLabelDescription($table, $field)
                )
            )
        );
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return string
     */
    private function buildId($table, $field)
    {
        return $table->getName().'_'.$field->getName();
    }
    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    public function buildSelectByEnum($table, $field){
        $options = array();
        foreach($field->getEnumValues() as $value) {
            $options[] = array(
                'id'=> $value,
                'value' => Word::get('forms', $table->getName().'_'.$field->getName().'_'.$value)
            );
        }
        $options[0]['selected'] = true;
        return $this->buildSelect($table, $field, $options);
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @param $options array
     * @return array
     */
    public function buildSelect($table, $field, $options)
    {

        return array(
            'type' => 'select',
            'id' => $this->buildId($table, $field),
            'label' => $this->getLabelDescription($table, $field),
            'attr' => array(
                'name' => $field->getName(),
                'data-table' => $table->getName(),
            ),
            'options' => $options
        );
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return string
     */
    private function getLabelDescription($table, $field)
    {
        return Word::get('forms', $table->getName().'_'.$field->getName());
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    public function buildTextarea($table, $field)
    {
        return array(
            'type' => 'textarea',
            'label' => $this->getLabelDescription($table, $field),
            'value' => '',
            'attr' => array(
                'name' => $field->getName(),
                'data-table' => $table->getName(),
            )
        );
    }
    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    private function buildInput($table, $field)
    {
        $name = $field->getName();
        if($name == 'password' || $name == 'pass') {
            $type = 'password';
        } elseif ($name == 'email') {
            $type = 'email';
        } elseif ($field->getType() == 'date') {
            $type = 'date';
        } elseif ($field->getType() == 'time') {
            $type = 'time';
        } elseif ($field->getType() == 'datetime') {
            $type = 'datetime';
        } else {
            $type = 'text';
        }
        $out = array(
            'type' => $type,
            'label' => $this->getLabelDescription($table, $field),
            'id' => $this->buildId($table, $field),
            'value' => '',
            'attr' => array(
                'name' => $field->getName(),
                'data-table' => $table->getName(),
                'size' => $field->getLength()
            )
        );
        $length = $field->getLength();
        if(!empty($length)) {
            $out['attr']['size'] = $length;
        }
        return $out;
    }

    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    public function buildRadioFromEnum($table, $field){
        $buttons = array();
        foreach($field->getEnumValues() as $value) {
            $buttons[] = array(
                'value' => $value,
                'label' => $this->getLabelDescription($table, $field).'_'.$value,
                'data-table' => $table->getName()
            );
        }
        return $this->buildRadio($table, $field, $buttons);
    }
    /**
     * @param $table ORM_Table
     * @param $field ORM_Table_Field
     * @return array
     */
    public function buildRadio($table, $field, $buttons){
        return array(
            'type' => 'radio',
            'id' => $this->buildId($table, $field),
            'name' => $field->getName(),
            'buttons' => $buttons
        );
    }

    public function convertSelectToRadioButtons($select){
        $buttons = array();
        foreach($select['options'] as $option) {
            $buttons[] = array(
                'value' => $option['id'],
                'label' => $option['value'],
                'data-table' => $option['data-table']
            );
        }
        return array(
            'type' => 'radio',
            'id' => !empty($select['id']) ? $select['id'] : null,
            'name' => $select['attr']['name'],
            'buttons' => $buttons
        );
    }

    /**
     * @param $bind ORM_Table_Bind
     * @param $item array
     */
    private function setAutocomplete($bind, &$item)
    {

    }
}