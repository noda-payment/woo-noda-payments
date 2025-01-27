<?php

namespace NodaPay\Payment\Responses;

use InvalidArgumentException;
use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\NodaPayment;

/**
 * Class Button represents a payment button in the Noda system.
 */
class Button implements GenericResponse
{
	/**
	 * SVG logo URL to use for the payment button.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Type of the payment option.
	 * Allowed values: 'bank', 'country', 'noda'.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Noda payment option identifier.
	 * For example, country code (gb, de, ...) or bank id (barclays, citibank, ...).
	 *
	 * @var string|null
	 */
	public $id = null;

	/**
	 * Alternative display name for the payment option.
	 * For example: "United Kingdom", "Lloyds Bank", "Spain".
	 *
	 * @var string|null
	 */
	public $displayName = null;

	/**
	 * Country code for the "bank" response types.
	 *
	 * @var string|null
	 */
	public $country = null;

	public function __construct($url, $type, $id = null, $displayName = null, $country = null)
    {
        $this->url = $url;
        $this->set_type($type);
        $this->id = $id;
        $this->displayName = $displayName;
        $this->country = $country;
    }

	/**
	 * Sets the type of the payment option.
	 * Ensures that the provided type is one of the allowed types.
	 *
	 * @param string $type Type of the payment option.
	 * @throws InvalidArgumentException If an invalid type is provided.
	 */
	private function set_type( string $type ) {
		if (!in_array( $type, NodaPayment::ALLOWED_TYPES, true ) ) {
			throw new InvalidArgumentException( 'Invalid type provided' );
		}

		$this->type = $type;
	}
}

