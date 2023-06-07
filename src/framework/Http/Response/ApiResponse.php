<?php

// use App\Lib\LoggingSpiralv2;

class ApiResponse
{
    public $data = null;
    public $count = 0;
    public $code = 0;
    public $message = null;
    public $header = [];
    public $result = false;

    public static ?Logger $logger = null;

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
            ],
            JSON_UNESCAPED_SLASHES
        );
        $this->logging();
        return $response;
    }

    public function logging()
    {
        global $SPIRAL;
        /*
        if( ! class_exists(LogConfig::class) ) { return ""; }
        if(! LogConfig::EXPORT_TO_SPIRALV2){ return ""; }
        $spiralv2 = new LoggingSpiralv2(LogConfig::SPIRALV2_API_KEY , 'https://api.spiral-platform.com/v1/');
        $spiralv2->setAppId(LogConfig::LOGGING_APP_TITLE);
        $spiralv2->setDbId(LogConfig::JOYPLA_API_LOGGING_DB_TITLE);
        $logger = new Logger($spiralv2);

        */
        if ($this::$logger) {
            if ($this->code != 200 && $this->code != 191) {
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
            }
        }
    }
}
