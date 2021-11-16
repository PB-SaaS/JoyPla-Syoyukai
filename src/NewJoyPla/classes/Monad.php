<?php

/**
 * 文脈も持つクラス関連を格納している名前空間
 * 参考URL -> https://qiita.com/7shi/items/547b6137d7a3c482fe68
 * ※　https://i02.smp.ne.jp/u/hrcloud/1.0.0/utils/php/field.txtに依存している
 */
namespace monad
{

    use Exception;

    abstract class Try_
    {
        abstract function getValue();

        function isSuccess()
        {
            return $this instanceof Success;
        }

        function isFailed()
        {
            return $this instanceof Failed;
        }

        function fold(callable $failedProcess, callable $successProcess) {
            if ($this->isSuccess()) {
                return $successProcess($this->getValue());
            } else if ($this->isFailed()) {
                return $failedProcess($this->getValue());
            } else {
                throw new Exception("dont match this instance object.");
            }
        }
    }

    class Success extends Try_
    {
        private $obj;

        function __construct($obj = null)
        {
            $this->obj = $obj;
        }

        public function getValue()
        {
            return $this->obj;
        }
    }

    class ValidateSuccess extends Success {}

    class Failed extends Try_
    {
        private $message;

        function __construct($message)
        {
            $this->message = $message;
        }

        function getValue()
        {
            return $this->message;
        }
    }

    class ValidateFailed extends Failed
    {
        function __construct($message)
        {
            parent::__construct($message);
        }
    }

    class InsertSuccess extends Success {}

    class InsertFailed extends Failed
    {
        function __construct($message)
        {
            parent::__construct($message);
        }
    }

    class TryList
    {
        private $tryList;

        function __construct(array $tryList=null)
        {
            if (is_null($tryList)) {
                $this->tryList = array();
            } else {
                $this->tryList = $tryList;
            }
        }

        function add(Try_ $t): void
        {
            $this->tryList[] = $t;
        }

        function getSuccessObjects(): array
        {
            return array_filter($this->tryList, function($e) { return $e instanceof Success; });
        }

        function getFailedObjects(): array
        {
            return array_filter($this->tryList, function($e) { return $e instanceof Failed; });
        }

        function countFailedObject(): int
        {
            return count($this->getFailedObjects());
        }

        function isValidateProcessTried(): bool
        {
            return $this->isValidateProcessTriedToSuccess() || $this->isValidateProcessTriedToFailed();
        }

        function isValidateProcessTriedToSuccess(): bool
        {
            $result = array_filter($this->tryList, function($e) { return $e instanceof ValidateSuccess; });
            return count($result) > 0;
        }

        function isValidateProcessTriedToFailed(): bool
        {
            $result = array_filter($this->tryList, function($e) { return $e instanceof ValidateFailed; });
            return count($result) > 0;
        }

        function isInsertProcessTried(): bool
        {
            return $this->isInsertProcessTriedToSuccess() || $this->isInsertProcessTriedToFailed();
        }

        function isInsertProcessTriedToSuccess(): bool
        {
            $result = array_filter($this->tryList, function($e) { return $e instanceof InsertSuccess; });
            return count($result) > 0;
        }

        function isInsertProcessTriedToFailed(): bool
        {
            $result = array_filter($this->tryList, function($e) { return $e instanceof InsertFailed; });
            return count($result) > 0;
        }

        function getList(): array
        {
            return $this->tryList;
        }
    }
}