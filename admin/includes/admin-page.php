<h1><?php echo __('Tapcha Plugin Settings', 'tapcha') ?></h1>

<?php settings_errors(); ?>

<form method="post" action="options.php">
    <?php settings_fields('tapcha_options_group'); ?>
    <?php do_settings_sections('tapcha-settings-page'); ?>
    <?php submit_button(); ?>
</form>

<div id="tapcha-stats-loading" class="loader"></div>
<div id="tapcha-stats">
    <h1><?php echo __('Statistics', 'tapcha') ?></h1>

    <table class="table table-hover table-striped stats-table">
        <tr>
            <td>
                <p><?php echo __('Success rate:', 'tapcha') ?></p>
                <h3 id="tapcha-success-rate"></h3>
            </td>
        </tr>
        <tr>
            <td>
                <p><?php echo __('Response rate:', 'tapcha') ?></p>
                <h3 id="tapcha-response-rate"></h3>
            </td>
        </tr>
        <tr>
            <td>
                <p><?php echo __('Number of challenges:', 'tapcha') ?></p>
                <h3 id="tapcha-number-of-challenges"></h3>
            </td>
        </tr>
        <tr>
            <td>
                <p><?php echo __('Number of responses:', 'tapcha') ?></p>
                <h3 id="tapcha-number-of-responses"></h3>
            </td>
        </tr>
    </table>
</div>