<?php 
/**
 * @var \model\record\mediaFile $item
 */
?>
<section class="evidence metadata" style="display: block;">
    <?php if ($item->getLocationMetadata() || $item->getLocationLuminosity() || $item->getLocationAirPressure()): ?>
    <section class="sensors">
        <div class="inner-content">
            <h2 class="uppercase">Location and device sensors reading:</h2>
            <ul>
                <?php if ($item->getLocationLat()): ?>
                <li>
                    <strong>Latitude:</strong> <?php echo fraction_to_min_sec($item->getLocationLat(), true); ?>
                </li>
                <?php endif ?>
                <?php if ($item->getLocationLng()): ?>
                <li>
                    <strong>Longitude:</strong> <?php echo fraction_to_min_sec($item->getLocationLng(), false); ?>
                </li>
                <?php endif ?>
                <?php if ($item->getLocationElevation()): ?>
                <li>
                    <strong>Elevation above sea level:</strong> <?php echo sprintf("%dm (%dft)", $this->escape($item->getLocationElevation()), 3.28084 * $this->escape($item->getLocationElevation())); ?>
                </li>
                <?php endif ?>
                <?php if ($item->getLocationLuminosity()): ?>
                <li>
                    <strong>Luminosity (ambient light):</strong> <?php echo sprintf('%1.1f', $this->escape($item->getLocationLuminosity())); ?>lux
                </li>
                <?php endif ?>
                <?php if ($item->getLocationAirPressure()): ?>
                <li>
                    <strong>Air pressure:</strong> <?php echo sprintf("%dmB (%dmmHg)", $this->escape($item->getLocationAirPressure()), $this->escape($item->getLocationAirPressure()) / 1.3332239); ?>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($item->getWifiAps()): ?>
    <section class="wifi-ap">
        <div class="inner-content">
            <h2 class="uppercase">Detected wifi access points in the proximity</h2>
            <ol>
            <?php
                $i = 0;
                foreach ($item->getWifiAPs() as $ap) {
                    if (++$i > 5) break;
            ?>
                <li><?php echo $this->escape($ap); ?></li>
            <?php
                }
            ?>
            </ol>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($item->getCellTowers()): ?>
    <section class="cell-towers">
        <div class="inner-content">
            <h2 class="uppercase">Mobile base stations in the proximity (cell towers):</h2>
            <ol>
            <?php 
                $i = 0;
                foreach ($item->getCellTowers() as $cellTower) {
                    if (++$i > 5) break;
            ?>
                    <li><?php echo $this->escape($cellTower); ?></li>
            <?php
                }
            ?>
            </ol>
        </div>
    </section>
    <?php endif; ?>
</section>