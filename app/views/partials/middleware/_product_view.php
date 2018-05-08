<table class="table table-middleware">
  <thead>
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Product code</th>
      <th>Size</th>
      <th>Price</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="max-width: 120px;">
        <!-- Product image and info -->
          <div class="thumbnail">
            <img src="/imglib_thumbnails<?=$product['image']?>">
          </div>
        <!-- / Product image and info -->
      </td>
      <td style="max-width: 300px;">
        <h3><?=$product['name']?></h3>
        <?=$product['info']?>
      </td>
      <td>
        <span class="visible-xs">Product code</span>
        <h4 class="mt-20"><?=$product['reference']?></h4>
      </td>
      <td>
        <span class="visible-xs">Size</span>
        <h4 class="mt-20"><?=$size_data['name']?></h4>
      </td>
      <td>
        <?if($product['price_with_sale']):?>
          <h3><?=number_format($product['price_with_sale']);?> <?=$website_data['currency']?> (<strike><?=$product['price']?> <?=$website_data['currency']?></strike>)</h3>
        <?else:?>
          <h3><?=number_format($product['price']);?> <?=$website_data['currency']?></h3>
        <?endif;?>
      </td>
    </tr>
  </tbody>
</table>
