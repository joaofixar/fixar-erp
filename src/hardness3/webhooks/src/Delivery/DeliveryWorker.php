<?php

declare(strict_types=1);

namespace HardnessWebhooks\Delivery;

final class DeliveryWorker
{
    /** @var DeliveryRepository */ private $repository;
    /** @var DeliveryDispatcherInterface */ private $dispatcher;
    /** @var int */ private $batchSize;

    public function __construct(DeliveryRepository $repository, DeliveryDispatcherInterface $dispatcher, int $batchSize = 25)
    {
        if ($batchSize < 1) { throw new \InvalidArgumentException('Webhook batch size must be positive.'); }
        $this->repository = $repository; $this->dispatcher = $dispatcher; $this->batchSize = $batchSize;
    }

    /** @return array{candidates:int,claimed:int,lost:int,dispatched:int,recovered:int} */
    public function runTenant(\mysqli $connection, string $workerId): array
    {
        $summary = array('candidates' => 0, 'claimed' => 0, 'lost' => 0, 'dispatched' => 0, 'recovered' => 0);
        foreach ($this->repository->findCandidateIds($connection, $this->batchSize) as $deliveryId) {
            ++$summary['candidates'];
            $wasExpiredLease = $this->repository->isExpiredLeaseCandidate($connection, $deliveryId);
            if (!$this->repository->tryClaim($connection, $deliveryId, $workerId)) { ++$summary['lost']; continue; }
            ++$summary['claimed'];
            if ($wasExpiredLease) { ++$summary['recovered']; }
            $delivery = $this->repository->loadClaimed($connection, $deliveryId, $workerId);
            if ($delivery === null) { ++$summary['lost']; continue; }
            $this->dispatcher->dispatch($connection, $workerId, $delivery);
            ++$summary['dispatched'];
        }
        return $summary;
    }
}
