<?php

namespace Oro\Bundle\CalendarBundle\Model\Recurrence;

use Oro\Bundle\CalendarBundle\Entity\Recurrence;

class DailyStrategy implements StrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOccurrences(Recurrence $recurrence, \DateTime $start, \DateTime $end)
    {
        $result = [];
        $occurrenceDate = $recurrence->getStartTime();
        $fromStartInterval = 1;
        if ($start > $occurrenceDate) {
            $dateInterval = $start->diff($occurrenceDate);
            $fromStartInterval = floor($dateInterval->format('%a') / $recurrence->getInterval());
            $fromStartInterval = $dateInterval->format('%r%h') < 0 ? $fromStartInterval + 1 : $fromStartInterval;
            $occurrenceDate = $this->getNextOccurrence($fromStartInterval * $recurrence->getInterval(), $occurrenceDate);
        }

        $occurrences = $recurrence->getOccurrences();
        while ($occurrenceDate <= $recurrence->getEndTime()
            && $occurrenceDate <= $end
            && (is_null($occurrences) || $fromStartInterval <= $occurrences)
        ) {
            $result[] = $occurrenceDate;
            $fromStartInterval++;
            $occurrenceDate = $this->getNextOccurrence($recurrence->getInterval(), $occurrenceDate);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Recurrence $recurrence)
    {
        return $recurrence->getRecurrenceType() === Recurrence::TYPE_DAILY;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurrencePattern(Recurrence $recurrence)
    {
        return 'daily';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'recurrence_daily';
    }

    /**
     * Returns occurrence date according to last occurrence date and recurrence interval.
     *
     * @param integer $interval
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    protected function getNextOccurrence($interval, \DateTime $date)
    {
        return new \DateTime("+{$interval} day {$date->format('c')}");
    }
}
