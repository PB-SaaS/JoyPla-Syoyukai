<?php
declare(strict_types=1);

namespace NGT\Barcode\GS1Decoder\Identifiers;

use DateTimeImmutable;
use NGT\Barcode\GS1Decoder\Identifiers\Abstracts\Identifier;

class ExpirationDateAndTimeIdentifier extends Identifier
{
    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'USE BY';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Expiration date and time';
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return '7003';
    }

    /**
     * @inheritDoc
     */
    public function getLength(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getFormat(): string
    {
        return '/^\d{10}$/';
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?DateTimeImmutable
    {
        $content = $this->parseDateTime();

        if ($content === false) {
            return null;
        }

        return $content;
    }

    /**
     * Parse the content into DateTimeImmutable.
     *
     * @return  DateTimeImmutable|false
     */
    private function parseDateTime()
    {
        if ($this->content === null) {
            return false;
        }

        if (strlen($this->content) === 10) {
            return DateTimeImmutable::createFromFormat('ymdHi', $this->content);
        }

        return false;
    }
}
