<?php declare(strict_types=1);

namespace NodaPay\Payment\Contracts;

use NodaPay\Payment\Abstracts\Response;

interface ApiCommand {

	public function execute(): Response;

}
