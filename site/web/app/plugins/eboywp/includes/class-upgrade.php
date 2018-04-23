<?php

class eboywp_Upgrade
{
    function __construct() {
        $this->version = eboywp_VERSION;
        $this->last_version = get_option( 'eboywp_version' );

        if ( version_compare( $this->last_version, $this->version, '<' ) ) {
            if ( version_compare( $this->last_version, '0.1.0', '<' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                $this->clean_install();
            }
            else {
                $this->run_upgrade();
            }

            update_option( 'eboywp_version', $this->version );
        }
    }


    private function clean_install() {
        global $wpdb;

        $sql = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}eboywp_index (
            id BIGINT unsigned not null auto_increment,
            post_id INT unsigned,
            eboy_name VARCHAR(50),
            eboy_source VARCHAR(255),
            eboy_value VARCHAR(50),
            eboy_display_value VARCHAR(200),
            term_id INT unsigned default '0',
            parent_id INT unsigned default '0',
            depth INT unsigned default '0',
            variation_id INT unsigned default '0',
            PRIMARY KEY (id),
            INDEX eboy_name_idx (eboy_name),
            INDEX eboy_source_idx (eboy_source),
            INDEX eboy_name_value_idx (eboy_name, eboy_value)
        ) DEFAULT CHARSET=utf8";
        dbDelta( $sql );

        // Add default settings
        $settings = file_get_contents( eboywp_DIR . '/assets/js/src/sample.json' );
        add_option( 'eboywp_settings', $settings );
    }


    private function run_upgrade() {
        global $wpdb;

        if ( version_compare( $this->last_version, '1.9', '<' ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}eboywp_index ADD COLUMN term_id INT unsigned default '0' AFTER eboy_display_value" );
            $wpdb->query( "UPDATE {$wpdb->prefix}eboywp_index SET term_id = eboy_value WHERE LEFT(eboy_source, 4) = 'tax/'" );
        }

        if ( version_compare( $this->last_version, '2.2.3', '<' ) ) {
            deactivate_plugins( 'eboywp-proximity/eboywp-proximity.php' );
            deactivate_plugins( 'eboywp-proximity-master/eboywp-proximity.php' );
        }

        if ( version_compare( $this->last_version, '2.7', '<' ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}eboywp_index ADD COLUMN variation_id INT unsigned default '0' AFTER depth" );
        }

        if ( version_compare( $this->last_version, '3.1.0', '<' ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}eboywp_index MODIFY eboy_name VARCHAR(50)" );
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}eboywp_index MODIFY eboy_value VARCHAR(50)" );
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}eboywp_index MODIFY eboy_display_value VARCHAR(200)" );
            $wpdb->query( "CREATE INDEX eboy_name_value_idx ON {$wpdb->prefix}eboywp_index (eboy_name, eboy_value)" );
        }
    }
}
