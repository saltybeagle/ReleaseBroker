<?php
class ReleaseBroker_PushHookPayload
{
    protected $payload;

    function __construct($json)
    {
        $this->setPayload($json);
    }

    /**
     * set the payload sent from the service hook
     * 
     * @see http://help.github.com/post-receive-hooks/
     * 
     * @param string $json
     */
    function setPayload($json)
    {
        $this->payload = json_decode($json);
    }

    /**
     * Check if the payload was a tag
     * 
     * @return bool
     */
    function tagWasAdded()
    {
        // @TODO, not sure how to check for this yet
        return false;
    }

    /**
     * Get the package name
     * 
     * return string eg: PEAR2_Pyrus_Developer
     */
    function getPackageName()
    {
        return $this->payload['repository']['name'];
    }

    /**
     * Get the repository URL
     * 
     * @return string
     */
    function getRepositoryURL()
    {
        return $this->payload['repository']['url'];
    }
    

    function getMainatinerEmail()
    {
        $author = $this->getCommitAuthor(count($this->payload['commits'])-1);
        return $author['email'];
    }

    function getMaintainerName()
    {
        $author = $this->getCommitAuthor(count($this->payload['commits'])-1);
        return $author['name'];
    }

    function getCommitAuthor($offset)
    {
        return $this->payload['commits'][$offset]['author'];
    }
}