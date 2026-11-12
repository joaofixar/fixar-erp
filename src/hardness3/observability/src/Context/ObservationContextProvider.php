<?php
declare(strict_types=1);
namespace Hardness\Observability\Context;
interface ObservationContextProvider { public function current(): ObservationContext; }
