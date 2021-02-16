<?php

namespace InWeb\Admin\App\Metrics;

use DateInterval;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Card;
use InWeb\Admin\App\Http\Requests\AdminRequest;

abstract class Metric extends Card
{
    use HasHelpText;

    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    public $name;

    /**
     * Indicates whether the metric should be refreshed when actions run.
     *
     * @var bool
     */
    public $refreshWhenActionRuns = false;

    /**
     * Calculate the metric's value.
     *
     * @param AdminRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function resolve(AdminRequest $request)
    {
        $resolver = function () use ($request) {
            return $this->onlyOnDetail
                    ? $this->calculate($request, $request->findModelOrFail())
                    : $this->calculate($request);
        };

        if ($cacheFor = $this->cacheFor()) {
            $cacheFor = is_numeric($cacheFor) ? new DateInterval(sprintf('PT%dS', $cacheFor * 60)) : $cacheFor;

            return Cache::remember(
                $this->getCacheKey($request),
                $cacheFor,
                $resolver
            );
        }

        return $resolver();
    }

    /**
     * Get the appropriate cache key for the metric.
     *
     * @param  AdminRequest  $request
     * @return string
     */
    protected function getCacheKey(AdminRequest $request)
    {
        return sprintf(
            'admin.metric.%s.%s.%s.%s.%s',
            $this->uriKey(),
            $request->input('range', 'no-range'),
            $request->input('timezone', 'no-timezone'),
            $request->input('twelveHourTime', 'no-12-hour-time'),
            $this->onlyOnDetail ? $request->findModelOrFail()->getKey() : 'no-resource-id'
        );
    }

    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Admin::humanize($this);
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int|void
     */
    public function cacheFor()
    {
        //
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name(), '-', null);
    }

    /**
     * Set whether the metric should refresh when actions are run.
     *
     * @param bool $value
     * @return Metric
     */
    public function refreshWhenActionRuns($value = true)
    {
        $this->refreshWhenActionRuns = $value;

        return $this;
    }

    /**
     * Prepare the metric for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'class' => get_class($this),
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
            'helpWidth' => $this->getHelpWidth(),
            'helpText' => $this->getHelpText(),
            'refreshWhenActionRuns' => $this->refreshWhenActionRuns,
        ]);
    }

    /**
     * Convert datetime to application timezone.
     *
     * @param  \Cake\Chronos\ChronosInterface|\Carbon\CarbonInterface  $datetime
     * @return \Cake\Chronos\ChronosInterface|\Carbon\CarbonInterface
     */
    protected function asQueryDatetime($datetime)
    {
        if (! $datetime instanceof \DateTimeImmutable) {
            return $datetime->copy()->timezone(config('app.timezone'));
        }

        return $datetime->timezone(config('app.timezone'));
    }
}
