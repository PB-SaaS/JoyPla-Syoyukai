<?php
namespace framework\Http;

/**
 * Class Response
 *
 * @package App\Http\Message
 */
class Response
{
    private array  $content;
    private string $statusCode;
    private string $statusText;
    private array  $headers;

    public function __construct(array $content, string $statusCode, string $statusText, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
        $this->headers = $headers;
    }

    final public function render(): void
    {
        //header('HTTP/1.1 ' . $this->statusCode . ' ' . $this->statusText);

        //foreach ($this->headers as $name => $value) {
            //header($name . ': ' . $value);
        //}
        echo $this->content;
    }

    final public function setContent(array $content): void
    {
        $this->content = $content;
    }

    final public function setStatusCode(string $statusCode, string $statusText = ''): void
    {
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
    }

    final public function setHttpHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}