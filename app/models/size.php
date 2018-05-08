<?php
class size {

  public function get($id) {
    return getSimpleList('size', false, 'id='.$id, false, 1)[0];
  }
}
