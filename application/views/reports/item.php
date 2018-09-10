<?php
/**
 * @var \model\record\report $item
 * @var \controller\reports $this 
 */


    if ($this->user->isAdmin()) {
        echo $this->partial('reports/admin.php', ['report' => $item]);
    }

    $evidenceWithLocation = null;
    foreach ($item->getEvidences() as $evidence) {
        if ($evidence->getLocationLat() && $evidence->getLocationLng()) {
            $evidenceWithLocation = $evidence;
            break;
        }
    }
?>

<article class="report">
    <div class="row">
        <div class="col-xs-12 col-md-10 col-md-push-1">        
            <header>
                <div class="col-header">
                    <h1><?php echo $this->escape($item->getTitle()); ?></h1>
                    <p class="uppercase">
                        Date of the report <time><?php echo date('n/j/Y', $item->getTs()); ?> 
                        <span class="hidden-xxs">&nbsp;&nbsp;</span><br class="visible-xxs">
                        time: <?php echo date('h:i a \G\M\T', $item->getTs()); ?></time>
                        <?php if ($item->getLocation(null)): ?>
                        <span class="hidden-xxs">&nbsp;&nbsp;</span><br class="visible-xxs">location: <?php echo $this->escape($item->getLocation()); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </header>
            
            <?php if ($item->getContent() || $item->getContactInfo() || $evidenceWithLocation): ?>
            <div class="row">
                <?php if ($item->getContent() || $item->getContactInfo()): ?>
                <div class="col-xxs-12 col-xs-6 col-sm-6">
                    <section class="intro dotme">
                        <p>
                            <?php if ($item->getContactInfo()): ?>
                                <strong>Contact information:</strong> 
                                <?php echo $this->escape($item->getContactInfo()); ?>
                                <br>
                            <?php endif; ?>
                            <?php if ($item->getContent()): ?>
                                <strong>Additional information:</strong>
                                <?php echo nl2br($this->escape($item->getContent())); ?> 
                                <a href="#" class="read-less">READ LESS</a></p>
                                <a href="#" class="read-more">READ MORE</a>
                            <?php endif; ?>
                    </section>
                </div>
                <?php endif; ?>
                
                <?php if ($evidenceWithLocation): ?>
                <div class="col-xxs-12 col-xs-6 col-sm-6">
                    <section class="map">
                       <div id='gmap_canvas'></div>
                    </section>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
                    
            <section class="evidences">
                <div class="col-header">
                    <h2>Attached evidence</h2>
                </div>
                <div class="row">
                    <?php 
                        foreach ($item->getEvidences() as $key => $evidence) {
                            echo '<div class="col-xxs-12 col-xs-6 col-sm-6">'; 
                            if ($evidence->id && $evidence->getIsUploaded()) {
                                echo $this->partial('reports/evidence/' . $evidence->getType() . '.php', ['item' => $item, 'evidence' => $evidence, 'key' => $key]);
                            } else {
                                echo $this->partial('reports/evidence/in-progress.php', ['item' => $item, 'evidence' => $evidence, 'key' => $key]);
                            }
                            echo $this->partial('reports/evidence/metadata.php', ['item' => $item, 'evidence' => $evidence, 'key' => $key]),
                            '</div>';
                        }
                    ?>
                    
                </div>
            </section>                
        </div>
    </div>
</article>

<?php 
if ($evidenceWithLocation):
?>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3bk-rc9002Vx5ouu9Ax2xDnn45baTFnY&callback=initMap">
</script>
<script type='text/javascript'>
    function initMap() {
        var latLng = new google.maps.LatLng(<?php echo $this->escape($evidence->getLocationLat()); ?>, <?php echo $this->escape($evidence->getLocationLng()); ?>), 
            myOptions = {
            zoom: <?php echo $item->getMapZoom(); ?>,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('gmap_canvas'), myOptions);
        marker = new google.maps.Marker({
            map: map,
            position: latLng
        });
        infowindow = new google.maps.InfoWindow(<?php echo json_encode([
            'content' => '<strong>' . $this->escape($item->getTitle()) . '</strong><br>' . $this->escape($item->getLocation()) . '<br>'
        ]); ?>);
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });
    }
</script>
<?php 
endif;
?>
