# yii2-jota-ics
Usage for create a new action with download a calendar File
```
$ics = new ICS([
                'dtstart' => Timestamp,
                'dtend' => Timestamp,
                'description' => Description,
                'summary' => Title
                ]);
$ics->Download();
```
