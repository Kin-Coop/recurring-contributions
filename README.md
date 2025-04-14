# CiviCRM Offline Recurring Contributions

#### This extension is about automatically creating contributions on a monthly (and then possibly weekly) basis.

It works by setting up an API4 endpoint that is triggered by a scheduled task that runs on a daily basis and duplicates any contributions it finds exactly one month ago for that day that are marked as recurring. So any contributions it finds a month ago it will then recreate for the current date.

It should also then trigger an email which goes to the contributor telling them about the contribution required.

It involves a couple of custom fields for contributions including the recurring frequency field and a link to a household field for the contribution.