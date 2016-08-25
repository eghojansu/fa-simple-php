<?php

namespace app;

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

        return $this;
    }

    /**
     * Add headers
     * @param array $headers
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $name = is_numeric($key) ? $value : $key;
            $content = is_numeric($key) ? null : $value;
            $this->addHeader($name, $content);
        }

        return $this;
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

        return $this;
    }

    /**
     * Reset header
     */
    public function clearHeader()
    {
        $this->headers = [];

        return $this;
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

        return $this;
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
