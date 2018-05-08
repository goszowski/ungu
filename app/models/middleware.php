<?php
class middleware {

  private $class_name = 'middleware_section';
  private $select_fields = ['type'];
  private $select_type =
    [
      'product_view' =>     false,
      'delivery' =>         ['class_name'=>'delivery_option', 'fields'=>['icon', 'affect_alternative']],
      'shipping_adress' =>  ['class_name'=>'shipping_input', 'fields'=>['type', 'required', 'fieldname', 'alternative']],
      'payment' =>          ['class_name'=>'payment_option', 'fields'=>['icon', 'is_active']],
    ];

  public function getSections() {
    $sections = getSimpleList($this->class_name, $this->select_fields, false, 'subtree_order ASC');
    if($sections) {
      foreach($sections as $key=>$section) {
        if($this->select_type[$section['type']]) {
          $sections[$key]['inner'] = getSimpleList($this->select_type[$section['type']]['class_name'], $this->select_type[$section['type']]['fields']);
        }
      }
    }

    return $sections;
  }
}
