<?php 
/**
 * @var \model\record\report $item
 * @var \model\record\evidence $evidence
 */
?>
<section class="evidence metadata">
    <?php if ($evidence->getLocationMetadata() || $evidence->getLocationLuminosity() || $evidence->getLocationAirPressure()): ?>
    <section class="sensors">
        <div class="inner-content">
            <h2 class="uppercase">Location and device sensors reading:</h2>
            <ul>
                <?php if ($evidence->getLocationLat()): ?>
                <li>
                    <strong>Latitude:</strong> <?php echo fraction_to_min_sec($evidence->getLocationLat(), true); ?>
                </li>
                <?php endif ?>
                <?php if ($evidence->getLocationLng()): ?>
                <li>
                    <strong>Longitude:</strong> <?php echo fraction_to_min_sec($evidence->getLocationLng(), false); ?>
                </li>
                <?php endif ?>
                <?php if ($evidence->getLocationElevation()): ?>
                <li>
                    <strong>Elevation above sea level:</strong> <?php echo sprintf("%dm (%dft)", $this->escape($evidence->getLocationElevation()), 3.28084 * $this->escape($evidence->getLocationElevation())); ?>
                </li>
                <?php endif ?>
                <?php if ($evidence->getLocationLuminosity()): ?>
                <li>
                    <strong>Luminosity (ambient light):</strong> <?php echo sprintf('%1.1f', $this->escape($evidence->getLocationLuminosity())); ?>lux
                </li>
                <?php endif ?>
                <?php if ($evidence->getLocationAirPressure()): ?>
                <li>
                    <strong>Air pressure:</strong> <?php echo sprintf("%dmB (%dmmHg)", $this->escape($evidence->getLocationAirPressure()), $this->escape($evidence->getLocationAirPressure()) / 1.3332239); ?>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($evidence->getWifiAps()): ?>
    <section class="wifi-ap">
        <div class="inner-content">
            <h2 class="uppercase">Detected wifi access points in the proximity</h2>
            <ol>
            <?php
                $i = 0;
                foreach ($evidence->getWifiAPs() as $ap) {
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

    <?php if ($evidence->getCellTowers()): ?>
    <section class="cell-towers">
        <div class="inner-content">
            <h2 class="uppercase">Mobile base stations in the proximity (cell towers):</h2>
            <ol>
            <?php 
                $i = 0;
                foreach ($evidence->getCellTowers() as $cellTower) {
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