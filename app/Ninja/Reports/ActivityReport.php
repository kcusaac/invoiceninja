<?php

namespace App\Ninja\Reports;

use App\Models\Activity;
use Auth;

class ActivityReport extends AbstractReport
{
    public $columns = [
        'date',
        'client',
        'user',
        'activity',
    ];

    public function run()
    {
        $account = Auth::user()->account;

        $startDate = $this->startDate;;
        $endDate = $this->endDate;

        $activities = Activity::scope()
            ->with('client.contacts', 'user', 'invoice', 'payment', 'credit', 'task', 'expense', 'account')
            ->whereRaw("DATE(created_at) >= \"{$startDate}\" and DATE(created_at) <= \"$endDate\"")
            ->orderBy('id', 'desc');

        foreach ($activities->get() as $activity) {
            $client = $activity->client;
            $this->data[] = [
                $activity->present()->createdAt,
                $client ? ($this->isExport ? $client->getDisplayName() : $client->present()->link) : '',
                $activity->present()->user,
                $this->isExport ? strip_tags($activity->getMessage()) : $activity->getMessage(),
            ];
        }


    }
}
