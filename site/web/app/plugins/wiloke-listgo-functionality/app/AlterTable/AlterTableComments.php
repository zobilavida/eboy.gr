<?php
namespace WilokeListGoFunctionality\AlterTable;

class AlterTableComments implements AlterTableInterface{
    protected $ratingCommentTbStatus  = 'wiloke_listgo_added_rating_column_to_comments_table';
    protected $ratingColumnName = 'listgo_rating';
    protected $ratingVersion = '1.0';

    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'createTable'));
        add_action('add_meta_boxes_comment', array($this, 'add_rating_box_to_comment'));
        add_action('wp_update_comment_data', array($this, 'update_comment_data'), 10, 3);
        add_action('manage_comments_custom_column' , array($this, 'print_rated_value'), 10, 2);
        add_filter('manage_edit-comments_columns', array($this, 'add_rating_column'));
    }

    public function add_rating_column($columns){
        return array_merge($columns, array('rating' => esc_html__( 'Rated', 'wiloke')));
    }

    /**
     * Adding Rating Column to Comments Table
     *
     * @since 1.0
     * @author Wiloke
     */
    public function print_rated_value($column, $commentID){
        if ($column === 'rating'){
            global $wpdb;
            $commentTbl = $wpdb->prefix . 'comments';
            $data = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT $this->ratingColumnName FROM $commentTbl WHERE comment_ID=%d",
                    $commentID
                )
            );

            echo $data;
        }
    }

    /**
     * Updating Rating Value
     *
     * @since 1.0
     * @author Wiloke
     * @refer https://core.trac.wordpress.org/browser/tags/4.7.3/src/wp-includes/comment.php
     */
    public function update_comment_data($data, $comment, $commentarr){
        if ( isset($data['newlistgo_rating']) && absint($data['newlistgo_rating']) > 6 ) {
            wp_die( esc_html__('Value must be less than or equal to 5', 'wiloke') );
        }

        global $wpdb;
        $wpdb->update(
            $wpdb->comments,
            array(
                $this->ratingColumnName => absint($data['newlistgo_rating'])
            ),
            array('comment_ID'=>$data['comment_ID']),
            array('%d'),
            array('%d')
        );

        return $data;
    }

    /**
     * Adding Rating Field into Comment Page in the admin area
     *
     * @since 1.0
     * @author Wiloke
     */
    public function add_rating_box_to_comment($comment){
        ?>
        <div class="stuffbox">
            <div class="inside">
                <fieldset>
                    <legend class="edit-comment-author"><?php esc_html_e( 'Rating', 'wiloke' ) ?></legend>
                    <table class="form-table editcomment">
                        <tbody>
                            <tr>
                                <td class="first" style="width: 10px;"><label for="wiloke_listgo_rating"><?php esc_html_e( 'Rated:', 'wiloke' ); ?></label></td>
                                <td><input min="0" max="5" type="number" name="newlistgo_rating" size="30" value="<?php echo esc_attr( $comment->listgo_rating ); ?>" id="wiloke_listgo_rating" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <br />
                </fieldset>
            </div>
        </div>
        <?php
    }


    /**
     * Adding Rating Column To Comment Table
     *
     * @since 1.0
     * @author Wiloke
     */
    public function createTable()
    {
        global $wpdb;

	    if ( get_option($this->ratingCommentTbStatus) || (version_compare(get_option($this->ratingCommentTbStatus), $this->ratingVersion, '>=')) ){
		    return false;
	    }

	    $commentTable = $wpdb->prefix . 'comments';
	    $wpdb->query("ALTER TABLE $commentTable ADD $this->ratingColumnName INT NOT NULL DEFAULT 0 AFTER user_id");
	    update_option($this->ratingCommentTbStatus, $this->ratingVersion);
    }

    public function deleteTable()
    {
        // TODO: Implement deleteTable() method.
    }
}