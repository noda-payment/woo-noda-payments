<?php declare(strict_types=1);

namespace NodaPay\Payment\Responses;

use NodaPay\Payment\Abstracts\GenericResponse;

/**
 * Class Url represents the payment URL of Noda system.
 */
class PayUrl implements GenericResponse
{
	/**
	 * Payment ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Payment URL.
	 *
	 * @var string
	 */
	private $url;

    /**
     * @var string
     */
	private $status;

	public function __construct(string $id, string $url, string $status)
    {
        $this->id = $id;
        $this->url = $url;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
