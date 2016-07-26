<?php

/**
 * Response helper
 */
class Response
{
    protected $headers = [];
    protected $content;

    /**
     * Add header
     * @param string $name
     * @param string $content
     */
    public function addHeader($name, $content = null)
    {
        $this->headers[$name] = $content;
    }

    /**
     * Send header
     * @return string
     */
    public function sendHeader()
    {
        foreach ($this->headers as $name => $content) {
            $header = $name.($content?": $content":'');
            header($header);
        }
        $this->clearHeader();
    }

    /**
     * Reset header
     */
    public function clearHeader()
    {
        $this->headers = [];
    }

    /**
     * Redirect to path
     * @param  string $path
     * @param  array  $param
     */
    public function redirect($path = null, array $param = [])
    {
        $this->addHeader('location', App::instance()->url($path, $param));
        $this->sendHeader();
        exit;
    }

    /**
     * Set response content
     * @param  string $content
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get response content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Clear content
     */
    public function clearContent()
    {
        $this->content = null;
    }

    /**
     * Send json
     * @param  string|object|array $data data to send
     */
    public function sendJSON($data)
    {
        $this->addHeader('Content-type', 'application/json');
        $this->setContent(is_string($data) ? $data : json_encode($data));
        $this->send();
    }

    /**
     * Send content
     * @return string
     */
    public function send()
    {
        $content = $this->getContent();
        $this->clearContent();
        $app = App::instance();
        if (!$app->get('headerOff')) {
            $this->sendHeader();
        }
        if (!$app->get('quiet')) {
            echo $content;

            if ($app->get('halt')) {
                die;
            }
        }

        return $content;
    }
}