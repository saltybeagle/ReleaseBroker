<?php
/**
 * PEAR2\ReleaseBroker\Main
 *
 * PHP version 5
 *
 * @category  Yourcategory
 * @package   PEAR2_ReleaseBroker
 * @author    Your Name <handle@php.net>
 * @copyright 2011 Your Name
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      http://svn.php.net/repository/pear2/PEAR2_ReleaseBroker
 */

/**
 * Main class for PEAR2_ReleaseBroker
 *
 * @category  Yourcategory
 * @package   PEAR2_ReleaseBroker
 * @author    Your Name <handle@php.net>
 * @copyright 2011 Your Name
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://svn.php.net/repository/pear2/PEAR2_ReleaseBroker
 */
namespace PEAR2\ReleaseBroker;
class Main
{
    protected $options = array('channel.xml' =>'channel.xml');
    protected $post    = array();
    function __construct($options = array(), $post = array())
    {
        $this->options = $options + $this->options;
        $this->post    = $post    + $this->post;
        
    }

    function processPush()
    {
        if (empty($this->post['payload'])) {
            throw new Exception('No payload to process');
        }
        $payload = new ReleaseBroker_PushHookPayload($this->post['payload']);

        if (!$payload->tagWasAdded()) {
            // Nothing to do, only process tags
            return;
        }

        $sourceDir   = $this->fetchReleaseFiles($payload->getRepositoryURL());
        $releaseFile = $this->packageRelease($sourceDir);
        $maintainer  = $this->getMaintainerByEmail($payload->getMainatinerEmail());
        $this->release($releaseFile, $maintainer);

    }

    function getMaintainerByEmail($email)
    {
    	// check maintainers and find match
    	return $maintainer;
    }

    /**
     * @return \PEAR2\Pyrus\Channel
     */
    function getChannel()
    {
    	return new \PEAR2\Pyrus\Channel(new \PEAR2\Pyrus\ChannelFile($this->options['channel.xml']));
    }

    /**
     * 
     * @param $channel
     * @param string $path
     * 
     * @return \PEAR2\SimpleChannelServer\Main
     */
    function getSimpleChannelServer($channel, $path)
    {
        return new \PEAR2\SimpleChannelServer\Main($channel, $path);
    }

    /**
     * Fetch the files from git to a tmp dir.
     *
     * @param string $repoURL the repo url
     * 
     * @return dir containing the source files
     */
    function fetchReleaseFiles($repoURL)
    {
        //@todo untested
        $work_dir = $this->getTmpDir();
        $git = new VersionControl_Git($work_dir);
        $git->clone($repoURL,
                    true, //bare
                    null);
        $tags = $git->getTags();

        // @todo somehow switch to the correct tag?
        return $work_dir;
    }

    function getTmpDir()
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR.md5(time());
    }

    function packageRelease($path)
    {
        $developer = new PEAR2\Pyrus\Developer\PackageFile\Commands();
        ob_start(); //ugh, wish this wasn't necessary
        $developer->package($frontend, $args, $options);
        $output = ob_get_clean();
        return 'hmm.. I dunno where this will come from'; //@todo
    }

    function release($relaseTgz, $maintainer)
    {
    	$channel = $this->getChannel();
        $scs     = $this->getSimpleChannelServer($channel, dirname($this->options['channel.xml']));
        return $scs->saveRelease($relaseTgz, $maintainer);
    }
}
