<?php

// Translations
$i18n = array(
    'All post types' => __( 'All post types', 'EWP' ),
    'Indexing complete' => __( 'Indexing complete', 'EWP' ),
    'Indexing' => __( 'Indexing', 'EWP' ),
    'Saving' => __( 'Saving', 'EWP' ),
    'Loading' => __( 'Loading', 'EWP' ),
    'Importing' => __( 'Importing', 'EWP' ),
    'Activating' => __( 'Activating', 'EWP' ),
    'Are you sure?' => __( 'Are you sure?', 'EWP' ),
    'Select some items' => __( 'Select some items', 'EWP' ),
);

// An array of facet type objects
$facet_types = EWP()->helper->facet_types;

// Clone facet settings HTML
$facet_clone = array();
foreach ( $facet_types as $name => $class ) {
    $facet_clone[ $name ] = __( 'This facet type has no additional settings.', 'EWP' );
    if ( method_exists( $class, 'settings_html' ) ) {
        ob_start();
        $class->settings_html();
        $facet_clone[ $name ] = ob_get_clean();
    }
}

// Settings
$settings_admin = new eboywp_Settings_Admin();
$settings_array = $settings_admin->get_settings();
$builder = $settings_admin->get_query_builder_choices();
$sources = EWP()->helper->get_data_sources();

?>

<script src="<?php echo eboywp_URL; ?>/assets/js/src/event-manager.js?ver=<?php echo eboywp_VERSION; ?>"></script>
<script src="<?php echo eboywp_URL; ?>/assets/js/src/query-builder.js?ver=<?php echo eboywp_VERSION; ?>"></script>
<script src="<?php echo eboywp_URL; ?>/assets/js/fSelect/fSelect.js?ver=<?php echo eboywp_VERSION; ?>"></script>
<?php
foreach ( $facet_types as $class ) {
    $class->admin_scripts();
}
?>
<script src="<?php echo eboywp_URL; ?>/assets/js/admin.js?ver=<?php echo eboywp_VERSION; ?>"></script>
<script>
EWP.i18n = <?php echo json_encode( $i18n ); ?>;
EWP.nonce = '<?php echo wp_create_nonce( 'EWP_admin_nonce' ); ?>';
EWP.settings = <?php echo json_encode( EWP()->helper->settings ); ?>;
EWP.clone = <?php echo json_encode( $facet_clone ); ?>;
EWP.builder = <?php echo json_encode( $builder ); ?>;
</script>
<link href="<?php echo eboywp_URL; ?>/assets/css/admin.css?ver=<?php echo eboywp_VERSION; ?>" rel="stylesheet">
<link href="<?php echo eboywp_URL; ?>/assets/js/fSelect/fSelect.css?ver=<?php echo eboywp_VERSION; ?>" rel="stylesheet">

<div class="eboywp-header">
    <span class="eboywp-logo" title="eboywp">&nbsp;</span>
    <span class="eboywp-version">v<?php echo eboywp_VERSION; ?></span>

    <span class="eboywp-header-nav">
        <a class="eboywp-tab" rel="basics"><?php _e( 'Basics', 'EWP' ); ?></a>
        <a class="eboywp-tab" rel="settings"><?php _e( 'Settings', 'EWP' ); ?></a>
        <a class="eboywp-tab" rel="support"><?php _e( 'Support', 'EWP' ); ?></a>
    </span>

    <span class="eboywp-actions">
        <span class="eboywp-response"></span>
        <a class="button eboywp-rebuild"><?php _e( 'Re-index', 'EWP' ); ?></a>
        <a class="button-primary eboywp-save"><?php _e( 'Save Changes', 'EWP' ); ?></a>
    </span>
</div>

<div class="wrap">

    <div class="eboywp-loading"></div>

    <!-- Basics tab -->

    <div class="eboywp-region eboywp-region-basics">
        <div class="eboywp-subnav">
            <span class="search-wrap">
                <input type="text" class="eboywp-search" placeholder="Search for a facet or template" />
            </span>
            <span class="btn-wrap hidden">
                <a class="button eboywp-back"><?php _e( 'Back', 'EWP' ); ?></a>
            </span>
        </div>

        <div class="eboywp-grid">
            <div class="eboywp-col content-facets">
                <h3>
                    Facets
                    <span class="eboywp-add">Add new</span>
                    <a class="icon-question" href="https://eboywp.com/documentation/facet-configuration/" target="_blank">?</a>
                </h3>
                <ul class="eboywp-cards"></ul>
            </div>

            <div class="eboywp-col content-templates">
                <h3>
                    Templates
                    <span class="eboywp-add">Add new</span>
                    <a class="icon-question" href="https://eboywp.com/documentation/template-configuration/" target="_blank">?</a>
                </h3>
                <ul class="eboywp-cards"></ul>
            </div>
        </div>

        <div class="eboywp-content"></div>
    </div>

    <!-- Settings tab -->

    <div class="eboywp-region eboywp-region-settings">
        <div class="eboywp-subnav">
            <?php foreach ( $settings_array as $key => $tab ) : ?>
            <a data-tab="<?php echo $key; ?>"><?php echo $tab['label']; ?></a>
            <?php endforeach; ?>
        </div>

        <?php foreach ( $settings_array as $key => $tab ) : ?>
        <div class="eboywp-settings-section" data-tab="<?php echo $key; ?>">
            <?php foreach ( $tab['fields'] as $field_data ) : ?>
            <table>
                <tr>
                    <td>
                        <?php echo $field_data['label']; ?>
                        <?php if ( isset( $field_data['notes'] ) ) : ?>
                        <div class="eboywp-tooltip">
                            <span class="icon-question">?</span>
                            <div class="eboywp-tooltip-content"><?php echo $field_data['notes']; ?></div>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $field_data['html']; ?></td>
                </tr>
            </table>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Support tab -->

    <div class="eboywp-region eboywp-region-support">
        <?php include( eboywp_DIR . '/templates/page-support.php' ); ?>
    </div>

    <!-- Hidden: clone settings -->

    <div class="hidden clone-facet">
        <div class="eboywp-row">
            <div class="table-row code-unlock">
                This facet is locked to prevent changes. <button class="unlock">Unlock now</button>
            </div>
            <table>
                <tr>
                    <td><?php _e( 'Label', 'EWP' ); ?>:</td>
                    <td>
                        <input type="text" class="facet-label" value="New facet" />
                        &nbsp; &nbsp;
                        <?php _e( 'Name', 'EWP' ); ?>: <span class="facet-name" contentEditable="true">new_facet</span>
                    </td>
                </tr>
                <tr>
                    <td><?php _e( 'Facet type', 'EWP' ); ?>:</td>
                    <td>
                        <select class="facet-type">
                            <?php foreach ( $facet_types as $name => $class ) : ?>
                            <option value="<?php echo $name; ?>"><?php echo $class->label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr class="eboywp-show name-source">
                    <td>
                        <?php _e( 'Data source', 'EWP' ); ?>:
                    </td>
                    <td>
                        <select class="facet-source">
                            <?php foreach ( $sources as $group ) : ?>
                            <optgroup label="<?php echo $group['label']; ?>">
                                <?php foreach ( $group['choices'] as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <hr />
            <table class="facet-fields"></table>
        </div>
    </div>

    <div class="hidden clone-template">
        <div class="eboywp-row">
            <div class="table-row code-unlock">
                This template is locked to prevent changes. <button class="unlock">Unlock now</button>
            </div>
            <div class="table-row">
                <input type="text" class="template-label" value="New template" />
                &nbsp; &nbsp;
                <?php _e( 'Name', 'EWP' ); ?>: <span class="template-name" contentEditable="true">new_template</span>
            </div>
            <div class="table-row">
                <div class="side-link open-builder"><?php _e( 'Open query builder', 'EWP' ); ?></div>
                <div class="row-label"><?php _e( 'Query Arguments', 'EWP' ); ?></div>
                <textarea class="template-query"></textarea>
            </div>
            <div class="table-row">
                <div class="side-link"><a href="https://eboywp.com/documentation/template-configuration/#display-code" target="_blank"><?php _e( 'What goes here?', 'EWP' ); ?></a></div>
                <div class="row-label"><?php _e( 'Display Code', 'EWP' ); ?></div>
                <textarea class="template-template"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Modal window -->

<div class="media-modal">
    <button class="button-link media-modal-close"><span class="media-modal-icon"></span></button>
    <div class="media-modal-content">
        <div class="media-frame">
            <div class="media-frame-title">
                <h1><?php _e( 'Query Builder', 'EWP' ); ?></h1>
            </div>
            <div class="media-frame-router">
                <div class="media-router">
                    <?php _e( 'Which posts would you like to use for the listing?', 'EWP' ); ?>
                </div>
            </div>
            <div class="media-frame-content">
                <div class="modal-content-wrap">
                    <div class="eboywp-modal-grid">
                        <div class="qb-area"></div>
                        <div class="qb-area-results">
                            <textarea class="qb-results" readonly></textarea>
                            <button class="button qb-send"><?php _e( 'Send to editor', 'EWP' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="media-modal-backdrop"></div>
