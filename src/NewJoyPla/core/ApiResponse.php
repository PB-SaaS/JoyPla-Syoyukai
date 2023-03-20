<?php

class ApiResponse
{
    public $data = null;
    public $count = 0;
    public $code = 0;
    public $message = null;
    public $header = [];
    public $result = false;

    public static Logger $logger;

    public function __construct(
        $data = null,
        $count = 0,
        $code = 0,
        $message = null,
        $header = []
    ) {
        $this->data = $data;
        $this->count = $count;
        $this->code = $code;
        $this->message = $message;
        $this->header = $header;
        if ($code == 0) {
            $this->result = true;
        }
    }

    public function toJson(): string
    {
        $response = json_encode(
            [
                'data' => $this->data,
                'count' => $this->count,
                'code' => $this->code,
                'message' => $this->message,
                'header' => $this->header,
                'result' => $this->result,
            ],
            JSON_UNESCAPED_SLASHES
        );
        $this->logging();
        return $response;
    }

    public function logging()
    {
        global $SPIRAL;
        if ($this::$logger) {
            if ($this->code != 0 && $this->code != 1) {
                $body = [
                    'execTime' => Logger::getTime(),
                    'AccountId' => $SPIRAL->getAccountId(),
                    'status' => 'ERROR',
                    'message' => json_encode(
                        [
                            'count' => $this->count,
                            'code' => $this->code,
                            'message' => $this->message,
                            'header' => $this->header,
                        ],
                        JSON_UNESCAPED_SLASHES
                    ),
                ];
                $this::$logger->out($body);
            } elseif ($this::$logger->getLevel() >= 3) {
                $body = [
                    'execTime' => Logger::getTime(),
                    'AccountId' => $SPIRAL->getAccountId(),
                    'status' => 'DEBUG',
                    'message' => json_encode(
                        [
                            'count' => $this->count,
                            'code' => $this->code,
                            'message' => $this->message,
                            'header' => $this->header,
                        ],
                        JSON_UNESCAPED_SLASHES
                    ),
                ];
                $this::$logger->out($body);
            }
        }
    }
}
