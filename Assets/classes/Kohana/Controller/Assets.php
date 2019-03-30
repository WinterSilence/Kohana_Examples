<?php
/**
 * Abstract assets controller class, uses for work with media files.
 *
 * @package    Common
 * @category   Controllers
 * @author     WinterSilence <info@handy-soft.ru>
 * @copyright  (c) 2013-2018 handy-soft.ru
 * @license    MIT
 */
abstract class Kohana_Controller_Assets extends Controller {

	/**
	 * - Find file in secure folder
	 * - Check path and file type
	 * - Copy file in public folder
	 * - Display file
	 * 
	 * @return void
	 */
	public function action_index()
	{
		// Load the configuration
		$config = Kohana::$config->load('assets');

		// Get the filename from current request
		$file = $this->request->param('file');

		extract(pathinfo($file), EXTR_SKIP);

		if (in_array($extension, $config['ignore_exts']))
		{
			throw new HTTP_Exception(415, 'Unsupported media type :ext', [':ext' => $extension]);
		}

		$filename = $dirname.DIRECTORY_SEPARATOR.$filename;
		$source_file = Kohana::find_file($config['source_dir'], $filename, $extension);
		
		if (empty($source_file))
		{
			throw new HTTP_Exception(404, 'File :name not found', [':name' => $filename]);
		}
		
		foreach ($config['ignore_dirs'] as $dir)
		{
			if (strpos($source_file, $dir) !== FALSE)
			{
				throw new HTTP_Exception(403, 'Access to file :name forbidden', [':name' => basename($source_file)]);
			}
		}
		
		// Get the application name from current request
		$application = $this->request->param('application');
		
		$assets_path = $config['assets_path'].DIRECTORY_SEPARATOR.$application.DIRECTORY_SEPARATOR.$dirname;
		
		if (!file_exists($assets_path))
		{
			// Create a file path in a public storage
			mkdir($assets_path, 0755, TRUE);
		}
		
		$assets_file = $assets_path.DIRECTORY_SEPARATOR.$basename;
		
		// Copy file in public storage
		copy($source_file, $assets_file);
		
		$filetime = filemtime($assets_file);
		// Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
		$this->check_cache(sha1($this->request->uri()).$filetime);
		// Send the file content as the response
		$this->response->body(file_get_contents($assets_file));
		// Set the proper headers to allow caching
		$this->response->headers([
			'content-type' => File::mime_by_ext($extension),
			'last-modified' => date('r', $filetime),
		];
	}
}
