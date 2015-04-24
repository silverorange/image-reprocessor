<?php

require_once 'Psr/Log/LoggerInterface.php';
require_once 'Console/CommandLine.php';
require_once 'Site/Site.php';
require_once 'Site/SiteApplication.php';
require_once 'Site/SiteDatabaseModule.php';
require_once 'Site/dataobjects/SiteImageSetWrapper.php';
require_once 'Site/dataobjects/SiteImageWrapper.php';

/**
 * Base class for an application
 *
 * @package   ImageReprocessor
 * @copyright 2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class ImageReprocessor extends SiteApplication
{
	// {{{ public properties

	/**
	 * @var MDB2_Driver_Common
	 */
	public $db = null;

	// }}}
	// {{{ protected properties

	/**
	 * @var SiteImageSet
	 */
	protected $image_set;

	/**
	 * @var SiteImageWrapper
	 */
	protected $images;

	// }}}
	// {{{ public function __construct()

	public function __construct(
		Console_CommandLine $parser,
		Psr\Log\LoggerInterface $logger
	) {
		parent::__construct('image-reprocessor', null);

		$this->logger = $logger;
		$this->parser = $parser;
	}

	// }}}
	// {{{ public function __invoke()

	/**
	 * Runs this application
	 *
	 * @return void
	 */
	public function __invoke()
	{
		$this->logger->setLevel(SiteCommandLineLogger::LEVEL_ERRORS);

		try {
			$this->cli = $this->parser->parse();
			$this->logger->setLevel($this->cli->options['verbose']);
			$this->initModules();

			if (!$this->getImageSet() instanceof SiteImageSet) {
				$this->logger->error(
					'Image set "{image_set}" does not exist in ' .
					'database.' . PHP_EOL,
					array(
						'image_set' => $this->cli->args['image_set']
					)
				);
				exit(1);
			}

			foreach ($this->getImages() as $image) {
				$this->reprocessImage($image);
			}

		} catch (Console_CommandLine_Exception $e) {
			$this->logger->error($e->getMessage() . PHP_EOL);
			exit(1);
		}
	}

	// }}}
	// {{{ public function run()

	/**
	 * Runs this application
	 *
	 * Interface required by SiteApplication.
	 *
	 * @return void
	 */
	public function run()
	{
		$this();
	}

	// }}}
	// {{{ protected function reprocessImage()

	protected function reprocessImage(SiteImage $image)
	{
		$this->logger->info(
			'Processing image {id} ... ',
			array(
				'id' => $image->id,
			)
		);

		$image->setFileBase($this->cli->args['directory']);
		$image->processMissingDimensionsFromLargestDimension();

		$this->logger->info('done' . PHP_EOL);
	}

	// }}}
	// {{{ protected function getImages()

	protected function getImages()
	{
		if (!$this->images instanceof SiteImageWrapper) {
			$this->images = SwatDB::query(
				$this->db,
				sprintf(
					'select * from Image
					where image_set = %s',
					$this->db->quote($this->getImageSet()->id, 'integer')
				),
				'SiteImageWrapper'
			);

			// Assign existing image set object for efficiency.
			foreach ($this->images as $image) {
				$image->image_set = $this->getImageSet();
			}
		}

		return $this->images;
	}

	// }}}
	// {{{ protected function getImageSet()

	protected function getImageSet()
	{
		if (!$this->image_set instanceof SiteImageSet) {
			$this->image_set = SwatDB::query(
				$this->db,
				sprintf(
					'select * from ImageSet
					where shortname = %s',
					$this->db->quote($this->cli->args['image_set'], 'text')
				),
				'SiteImageSetWrapper'
			)->getFirst();

			// Set CDN flag on image set before processing.
			$this->image_set->use_cdn = $this->cli->options['cdn'];
		}

		return $this->image_set;
	}

	// }}}
	// {{{ protected function initModules()

	protected function initModules()
	{
		foreach ($this->modules as $id => $module) {
			if ($id === 'database') {
				$module->dsn = $this->cli->args['dsn'];
				try {
					$module->init();
					$this->db = $module->getConnection();
				} catch (SwatDBException $e) {
					$this->logger->error(
						'Unable to connect using DSN {dsn}.' . PHP_EOL,
						array(
							'dsn' => $module->dsn
						)
					);
					exit(1);
				}
			} else {
				$module->init();
			}
		}
	}

	// }}}
	// {{{ protected function getDefaultModuleList()

	protected function getDefaultModuleList()
	{
		return array(
			'database' => 'SiteDatabaseModule'
		);
	}

	// }}}
}

?>
