<?php
$products = array(); // Products retreived from database

$is_active = true; // Only true for the first iteration
$i = 0;
?>
<div id="carouselExampleIndicators" class="carousel" data-ride="carousel">

<div class="carousel-inner">
<?php foreach($products as $p):?>
<?php if ($i % 4 == 0):?>
    <div class="item<?php if ($is_active) echo ' active'?>">
<?php endif?>
        <div class="row">
            <div class="col-sm-3">
                <div class="col-item">
                    <div class="photo">
                      <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto slider-image')); ?>
                    </div>
                </div>
            </div>
        </div>
<?php if (($i+1) % 4 == 0 || $i == count($products)-1):?>
    </div>
<?php endif?>
<?php
$i++;
if ($is_active) $is_active = false;
endforeach;
?>
</div>
</div>
