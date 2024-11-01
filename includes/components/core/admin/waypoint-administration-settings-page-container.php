<?php
namespace Waypoint\Core\Administration;

/**
 * Class Waypoint_Administration_Settings_Page_Container
 *
 * This is the main settings page container.
 *
 * Extend the Waypoint_Administration_Settings_Page_Abstract
 * to add tabs and pages to the settings page.
 *
 * @package Waypoint\Core\Administration
 */
class Waypoint_Administration_Settings_Page_Container
{
	/**
	 * @var static;
	 */
	private static $instance;

	/**
	 * @var array An array of callbacks matching keys.
	 */
	protected $sub_pages = [];

	/**
	 * Waypoint_Location_Settings_Page constructor.
	 */
	private function __construct()
	{}

	public static function get_instance()
	{
		if ( null === static::$instance )
		{
			static::$instance = new static;
			static::$instance->initialize();
		}

		return static::$instance;
	}

	public function initialize()
	{
		add_action( 'admin_menu', [ $this, 'register_submenu_page' ] );
	}

	public function get_sub_pages()
	{
		return apply_filters( 'waypoint_location_settings_submenu_pages_callbacks', $this->sub_pages );
	}

	/**
	 * @var string
	 */
	protected $parent_slug = 'edit.php?post_type=waypoint_location';

	/**
	 * @var string
	 */
	protected $menu_slug = 'waypoint-settings';

	/**
	 * Registers the sub menu page.
	 */
	public function register_submenu_page()
	{
		add_submenu_page(
			$this->parent_slug,
			'Settings',
			'Settings',
			'manage_options',
			$this->menu_slug,
			[$this, 'render_html']
		);
	}

	/**
	 * @return string
	 */
	public function get_current_tab()
	{
		if ( ! isset( $_GET['tab'] ) || empty( $_GET['tab'] ) ) {
            $tabs = $this->get_tabs();
            $keys = array_keys( $tabs );
            return $keys[0];
		}

		return $_GET['tab'];
	}

	public function get_tab_url( $key )
	{
		return "{$this->parent_slug}&page={$this->menu_slug}&tab={$key}";
	}

	/**
	 * @return array
	 */
	public function get_tabs()
	{
		return apply_filters( 'waypoint_location_settings_admin_tabs', [] );
	}

	public function print_active_tab_class( $key )
	{
		echo $key === $this->get_current_tab() ? 'nav-tab-active' : '';
	}

	public function render_tabs()
	{
		?>
		<h2 class="nav-tab-wrapper">
			<?php foreach( $this->get_tabs() as $key => $tab ) : ?>
				<a href="<?php echo $this->get_tab_url( $key ); ?>" class="nav-tab <?php $this->print_active_tab_class( $key ); ?>"><?php echo $tab; ?></a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	public function render_html()
	{
	    $this->save_settings();
	    ?>
		<div class="wrap">
			<h2>Waypoint Settings</h2>
			<?php $this->render_tabs(); ?>
            <form method="post" enctype="multipart/form-data">
	            <?php $this->render_submenu_page_callback(); ?>
            </form>
		</div>
		<?php
	}

	public function save_settings() {
        do_action( 'waypoint_save_administration_settings' );
	}

	public function render_submenu_page_callback() {

		$tab = $this->get_current_tab();

		$subpages = $this->get_sub_pages();

		if ( isset( $subpages[$tab] ) && is_callable( $subpages[$tab] ) ) {
			$subpages[$tab]();
			return;
		}

		$method = 'tab_' . $tab;
		if ( method_exists( $this, $method ) ) {
			$this->{$method}();
			return;
		}

		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'No callback exists for tab: ' . $tab , 'waypoint' ); ?></p>
        </div>
        <?php
	}
}