<?php declare( strict_types=1 );

namespace NodaPay\Payment\Responses;

use InvalidArgumentException;
use NodaPay\Payment\Abstracts\GenericResponse;
use NodaPay\Payment\NodaPayment;

/**
 * Class Status represents the status of a payment in the Noda system.
 */
class Status implements GenericResponse {

	/**
	 * Payment amount.
	 *
	 * @var int
	 */
	public $amount;

	/**
	 * Payment currency.
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Payment identifier in the Noda system.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Payment status.
	 *
	 * Options:
	 * - New: Initial state right after creation.
	 * - Processing: Payment initiation with the user's bank.
	 * - Awaiting Confirmation: Awaiting the payment confirmation by a customer in the bank's interface.
	 * - Done: Payment successfully processed.
	 * - Failed: Payment failed (abandoned by the customer or rejected by the bank).
	 *
	 * @var string Allowed values: New, Processing, Failed, Awaiting Confirmation, Done.
	 */
	public $status;

	/**
	 * Optional description for the payment.
	 *
	 * @var string|null
	 */
	public $description = null;

	/**
	 * Sets the status of the payment.
	 * Ensures that the provided status is one of the allowed types.
	 *
	 * @param string $status Type of the payment option.
	 *
	 * @throws InvalidArgumentException If an invalid type is provided.
	 */
	protected function set_status( string $status ) {
		if ( ! in_array( $status, NodaPayment::ALLOWED_STATUSES ) ) {
			throw new InvalidArgumentException( 'Invalid type provided' );
		}
		$this->status = $status;
	}
}

