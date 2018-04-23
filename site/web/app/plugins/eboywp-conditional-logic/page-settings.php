<script src="<?php echo EWPCL_URL; ?>/assets/js/admin.js?ver=<?php echo EWPCL_VERSION; ?>"></script>
<link href="<?php echo eboyWP_URL; ?>/assets/css/admin.css?ver=<?php echo eboyWP_VERSION; ?>" rel="stylesheet">
<link href="<?php echo EWPCL_URL; ?>/assets/css/admin.css?ver=<?php echo EWPCL_VERSION; ?>" rel="stylesheet">
<script>
EWPCL.rulesets = <?php echo json_encode( $this->rulesets ); ?>;
</script>

<div class="eboywp-header">
    <span class="eboywp-logo" title="EboyWP">&nbsp;</span>
    <span class="eboywp-header-nav">
        <a class="eboywp-tab" rel="rulesets"><?php _e( 'Rulesets', 'ewp' ); ?></a>
        <a class="eboywp-tab" rel="settings"><?php _e( 'Settings', 'ewp' ); ?></a>
    </span>
</div>

<div class="wrap">
    <div class="eboywp-response"></div>

    <div class="eboywp-region eboywp-region-rulesets">
        <div class="flexbox">
            <a class="button add-ruleset">Add Ruleset</a>
            <a class="button eboywp-save" style="margin-left:10px">Save Changes</a>
            <span class="ewpcl-response dashicons"></span>
        </div>

        <div class="eboywp-content-wrap"></div>
    </div>

    <div class="eboywp-region eboywp-region-settings">
        <div class="eboywp-content-wrap">
            <p class="description">To export, copy the code below.</p>
            <input type="text" class="export-code" readonly="readonly" />
            <p class="description" style="margin-top:20px">To import, paste code into the field below.</p>
            <textarea class="import-code"></textarea>
            <p class="description" style="color:red"><strong>NOTE:</strong> importing will replace any existing rulesets.</p>
            <input type="button" class="button ewpcl-import" value="Process Import" />
            <span class="ewpcl-import-response"></span>
        </div>
    </div>

    <!-- [Begin] Clone HTML -->

    <div class="clone hidden">
        <div class="clone-ruleset">
            <div class="ruleset">
                <table class="header-bar">
                    <tr>
                        <td class="toggle"><span class="dashicons dashicons-menu"></span></td>
                        <td class="title"><span class="ruleset-label" contenteditable="true">Edit me</span></td>
                        <td class="delete"><span class="dashicons dashicons-no-alt"></span></td>
                    </tr>
                </table>
                <table class="logic-row">
                    <tr>
                        <td class="conditions-col" style="width:60%">
                            <div class="td-label">Conditions</div>
                            <div class="condition-wrap">
                                <table class="ruleset-event">
                                    <tr>
                                        <td class="type">ON</td>
                                        <td class="logic">
                                            <select class="ruleset-on">
                                                <option value="loaded">Loaded</option>
                                                <option value="refresh">Refresh</option>
                                                <option value="refresh-loaded">Refresh &amp; Loaded</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <button class="button condition-and">Add Condition</button>
                        </td>
                        <td class="actions-col" style="width:40%">
                            <div class="td-label">Actions</div>
                            <div class="action-wrap"></div>
                            <button class="button action-and">Add Action</button>
                        </td>
                    <tr>
                </table>
            </div>
        </div>

        <div class="clone-condition">
            <table class="condition">
                <tr>
                    <td class="spacer"></td>
                    <td class="drop">
                        <span class="dashicons dashicons-no-alt condition-drop"></span>
                    </td>
                    <td class="type">IF</td>
                    <td class="logic">
                        <select class="condition-object">
                            <optgroup label="Basic">
                                <option value="eboys-empty">No eboys in use</option>
                                <option value="eboys-not-empty">Some eboys in use</option>
                                <option value="uri">Page URI</option>
                                <option value="total-rows">Result count</option>
                            </optgroup>
                            <optgroup label="eboy Value">
<?php foreach ( $this->eboys as $eboy ) : ?>
                                <option value="eboy-<?php echo $eboy['name']; ?>">eboy: <?php echo $eboy['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Template">
<?php foreach ( $this->templates as $template ) : ?>
                                <option value="template-<?php echo $template['name']; ?>">Template: <?php echo $template['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                        </select>
                        <select class="condition-compare">
                            <option value="is">is</option>
                            <option value="not">is not</option>
                        </select>
                        <input type="text" class="condition-value" placeholder="enter values" title="comma-separate multiple values"></input>
                    </td>
                    <td class="btn">
                        <button class="button condition-or">OR</button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="clone-action">
            <table class="action">
                <tr>
                    <td class="drop">
                        <span class="dashicons dashicons-no-alt action-drop"></span>
                    </td>
                    <td class="type">AND</td>
                    <td class="logic">
                        <select class="action-toggle">
                            <option value="show">Show</option>
                            <option value="hide">Hide</option>
                        </select>
                        <select class="action-object">
                            <option value="template">Template</option>
                            <option value="eboys">All eboys</option>
                            <optgroup label="eboys">
<?php foreach ( $this->eboys as $eboy ) : ?>
                                <option value="eboy-<?php echo $eboy['name']; ?>">eboy: <?php echo $eboy['label']; ?></option>
<?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Custom">
                                <option value="custom">Selector</option>
                            </optgroup>
                        </select>
                        <textarea class="action-selector hidden"></textarea>
                        <button class="button action-selector-btn"><span class="dashicons dashicons-editor-code"></span></button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- [End] Clone HTML -->

</div>

<!-- Modal window -->

<div class="media-modal">
    <button class="button-link media-modal-close"><span class="media-modal-icon"></span></button>
    <div class="media-modal-content">
        <div class="media-frame">
            <div class="media-frame-title">
                <h1><?php _e( 'Custom Selectors', 'ewp' ); ?></h1>
            </div>
            <div class="media-frame-router">
                <div class="media-router">
                    Add jQuery selectors (one per line). You can also use <strong>$EMPTY</strong> to select empty eboys.
                </div>
            </div>
            <div class="media-frame-content">
                <div class="modal-content-wrap">
                    <textarea class="action-selector-input" placeholder="$('.eboywp-eboy-categories')"></textarea>
                    <input type="button" class="button selector-save" value="Save Changes" />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="media-modal-backdrop"></div>
